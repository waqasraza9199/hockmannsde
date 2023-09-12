import deDE from '../../../module/articlequestions/snippet/de-DE.json';
import enGB from '../../../module/articlequestions/snippet/en-GB.json';
import template from './nimbits-articlequestions-detail.html.twig';

const {Application, Component, Mixin} = Shopware;
const {Criteria} = Shopware.Data;

Component.register('nimbits-articlequestions-detail', {
    template,

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    inject: [
        'repositoryFactory',
        'context'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    watch: {
        articlequestion: {
            handler() {
                //this.updateIsTitleRequired();
            },
            immediate: true,
            deep: true
        }
    },

    props: {
        pricerequestId: {
            type: String,
            required: false,
            default: null
        },
        article: {
            type: Object,
            required: false,
            default: null
        },
        articlequestion: {
            type: String,
            required: true,
            default: null
        }
        ,
        answer: {
            type: String,
            required: true,
            default: null
        }
    },

    data() {
        return {
            pricerequest: {},
            pricerequestbasket: {},

            isLoading: false,
            pricerequestId: null,
            processSuccess: false,

            repository: null,
            basketrepository: null,

            criteria: null,
            term: this.$route.query ? this.$route.query.term : null,

            httpClient: null,
            salesChannels: []
        };
    },
    computed: {
        aqRepository() {
            return this.repositoryFactory.create('nimbits_articlequestions');
        },
        articleRepository() {
            return this.repositoryFactory.create('product');
        }
    },


    created() {
        this.repository = this.repositoryFactory.create('nimbits_articlequestions');
        this.loadEntityData();

        const initContainer = Application.getContainer('init');
        this.httpClient = initContainer.httpClient;

        this.repositoryFactory.create('sales_channel')
            .search(new Criteria(), Shopware.Context.api)
            .then((channels) => {
                this.salesChannels = channels;

            });
    },

    methods: {
        loadEntityData() {
            this.isLoading = true;
            this.articlequestionId = this.$route.params.id;

            if (this.articlequestionId == null || typeof this.articlequestionId === "undefined") {
                this.articlequestion = this.aqRepository.create(Shopware.Context.api);
                this.isLoading = false;
            } else {
                this.articlequestion = this.aqRepository.get(this.articlequestionId, Shopware.Context.api).then((articlequestion) => {
                    this.articlequestion = articlequestion;

                    this.articleRepository.get(this.articlequestion.article_id, Shopware.Context.api).then((article) => {

                        this.article = article;
                        this.isLoading = false;
                    });


                });
            }
        },
        onSave() {
            this.isLoading = true;

            this.repository
                .save(this.articlequestion, Shopware.Context.api)
                .then(() => {

                    this.loadEntityData();
                    this.isLoading = false;
                    this.processSuccess = true;
                    this.createNotificationSuccess({
                        title: this.$t('nb-articlequestions.detail.successMessage.noti_title'),
                        message: this.$t('nb-articlequestions.detail.successMessage.noti_message')
                    });


                    var recipients = {};
                    recipients["TMP@nimbits.de"] = "TMP@nimbits.de";

                    this.articlequestion.recipients = recipients;
                    this.articlequestion.contentHtml = "TMP";
                    this.articlequestion.contentPlain = "TMP";
                    this.articlequestion.subject = this.$t('nb-articlequestions.detail.mailsubject');
                    this.articlequestion.senderName = this.$t('nb-articlequestions.detail.mailsender');
                    this.articlequestion.salesChannelId = this.salesChannels.getIds()[0];
                    this.articlequestion.id = this.articlequestionId;
                    this.articlequestion.isArticleQuestion = true;

                    var headers = {
                        Accept: 'application/vnd.api+json',
                        Authorization: `Bearer ${Shopware.Context.api.authToken.access}`,
                        'Content-Type': 'application/json'
                    };

                    this.httpClient
                        .post('nimbits/articlequestions/sendcustomermail', this.articlequestion, {headers})
                        .then((response) => {
                            this.createNotificationSuccess({
                                title: this.$t('nb-articlequestions.detail.successMessage.mail_noti_title'),
                                message: this.$t('nb-articlequestions.detail.successMessage.mail_noti_message')
                            });
                        });


                    this.$router.push({name: 'nb.articlequestions.overview'});
                }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    title: this.$t('nb-articlequestions.detail.error.noti_title'),
                    message: exception
                });
            });
        },

        saveFinish() {
            this.processSuccess = false;
        }
    }
});

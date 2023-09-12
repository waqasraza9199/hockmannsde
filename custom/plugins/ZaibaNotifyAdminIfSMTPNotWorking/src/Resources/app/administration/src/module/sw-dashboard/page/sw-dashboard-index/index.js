const { Component, Mixin} = Shopware;

Component.override('sw-dashboard-index', {

    inject: ['systemConfigApiService'],

    data(){
      return {
          config: {}
      }
    },

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('placeholder')
    ],

    mounted(){


        this.systemConfigApiService.getValues('ZaibaNotifyAdminIfSMTPNotWorking', null)
            .then(values => {
                let config = values;

                console.log(config);
                if(config['ZaibaNotifyAdminIfSMTPNotWorking.config.smtpError']){

                    this.createNotificationError({
                        message: 'SMTP not working',
                    });

                    setInterval(() => {
                        this.createNotificationError({
                            message: 'SMTP not working',
                        });
                    }, 5000);

                }
            })


    }
});

import ArticleQuestions from './articlequestions/articlequestions';

// Register them via the existing PluginManager
const PluginManager = window.PluginManager;
PluginManager.register('ArticleQuestions', ArticleQuestions, '.nimbits-ask-question-button');

// Necessary for the webpack hot module reloading server
if (module.hot) {
    module.hot.accept();
}

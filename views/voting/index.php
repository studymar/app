<?php

/* 
 * Startseite der Votings für EDIT
 */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\assets\VueAsset;
VueAsset::register($this);


if(YII_ENV_TEST)
    $env_vuecomponent_dir = __DIR__.'/../../web/vue-components/voting/';
else $env_vuecomponent_dir = "vue-components/voting/";

include_once($env_vuecomponent_dir.'VotingTopicListComponent.php');
include_once($env_vuecomponent_dir.'VotingTopicComponent.php');
include_once($env_vuecomponent_dir.'VotingQuestionComponent.php');

?>

    <div class="" id="voting">
        <div class="d-flex justify-content-between title-block">
            <h2>Votings</h2>
        </div>
        <div class="content-block">
        
            <div class="col-sm-12 h-100 p-0 row">
                <div class="col-sm-4" v-bind:class="{ 'hiddenOnSmall': view != null }" >
                    <voting-topic-list-component ref="votingTopicList"
                        v-bind:topic="topic"
                        >
                    </voting-topic-list-component>
                </div>
                <div class="col-sm-8" v-bind:class="{ 'hiddenOnSmall': view == null }" >
                    <p class="alert alert-light mt-1 notOnSmall" v-if="view == null && topic == null">Bitte wählen Sie eine Veranstaltung aus</p>                   
                    
                    <voting-topic-component
                        v-if="view == 'topic' && topic != null"
                        v-bind:topic="topic"
                        v-bind:votingweight="votingweight"
                        ref="topic"
                        >
                    </voting-topic-component>
                    
                    <voting-question-component
                        v-if="view == 'vote' && question != null"
                        v-bind:topic="topic"
                        v-bind:question="question"
                        v-bind:votingweight="votingweight"
                        ref="question"
                        >
                    </voting-question-component>
                </div>
            </div>
        </div>
        <br/><br/>

    </div>

    <script type="text/javascript">    
    new Vue({
        el: '#voting',
        components: {
            votingTopicListComponent,
            votingTopicComponent,
            votingQuestionComponent
        },
        data: {
            view: null,
            items: [
            ],
            topic: null,
            question: null,
            votingweight: null
        },
        mounted: function() {
        },
        // define methods under the `methods` object
        methods: {
            getTopic(item) {
                this.topic = item
                this.question = null
                this.view = 'topic'
            },
            closeTopic() {
                this.topic = null
                this.view = null
            },
            openVote(item){
                //topic beibehalten
                this.question = item
                this.view = 'vote'     
            },
            closeVote(){
                //topic beibehalten
                this.question = null
                this.view = 'topic'
            },
            setVotingweight(vw){
                this.votingweight = vw
            }
        }
    });
        
    </script>
    

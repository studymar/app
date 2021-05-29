<?php
use yii\helpers\Url;

/**
 */
/* vue laden */
use app\assets\VueAsset;
VueAsset::register($this);

/*
if(YII_ENV_TEST)
    $env_vuecomponent_dir = __DIR__.'/../../web/vue-components/voting/';
else $env_vuecomponent_dir = "vue-components/voting/";
*/
//include_once($env_vuecomponent_dir.'TeaserlistSortComponent.php');

?>

<template id="voting-topic-component">
    <div id="votingTopicComponent" class="p-2">
        
        <div class="d-flex justify-content-between title-block">
            <h3 class="">
                <a href="" class="onlySmall" v-on:click.prevent="$parent.closeTopic()">
                    <i class="material-icons">keyboard_arrow_left</i>
                </a>
                {{topic.headline}}
            </h3>
            <br/>
        </div>

        <div v-if="topic.votingweights.length == 0 || (votingweight != null && topic.votingweights.length>0)">
            <div v-if="votingweight != null">Abstimmung als: {{votingweight.name}}</div>

            <p class="alert alert-danger" v-if="config.topErrorLoading">{{config.topErrorLoadingMessage}}</p>
            <p class="alert alert-light" v-if="items == null || items.length == 0">{{config.topNoVotings}}</p>

            <div class="list-group" v-if="items != null && items.length>0" >
               <span v-for="(item,index) in items" :key="item.id">
                   <div class="list-group-item rounded mb-1" v-bind:class="[{'list-group-item-action': (item.hasAnswered.length==0 || item.hasAnswered==0)},{'inactive': item.hasAnswered.length>0}]">
                        <a href="" class="stretched-link" v-on:click.prevent="$parent.openVote(item)" v-if="item.hasAnswered.length==0 || item.hasAnswered==0"></a>
                        <div class="d-flex w-100 justify-content-between">
                          <div class="mb-1 fs-5">{{item.question}}</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-1 bigicon p-0">
                                <span>
                                    <span class="material-icons green" v-if="item.active == 1">play_circle_outline</span>
                                    <span class="material-icons red" v-else >pause_circle_outline</span>
                                </span>
                            </div>
                            <div class="col-sm-10 mb-1">
                                <ul class="property-list">
                                    <li v-if="item.votingtype && (item.hasAnswered == 0 || item.hasAnswered.length==0)">
                                        <span class="material-icons">east</span>
                                        {{item.votingtype.description}}
                                    </li>
                                    <li v-if="item.votingtype && item.hasAnswered.length>0">
                                        <span class="material-icons">east</span>
                                        <span>Ihre Antwort: </span>
                                        <span v-for="(answer,index) in item.hasAnswered" :key="answer">{{answer}}{{(index < (item.hasAnswered.length-1))?' | ':''}}</span>
                                    </li>
                                    <li v-if="item.hasAnswered.length>0">
                                        <span class="material-icons green">check</span>
                                        bereits abgestimmt
                                    </li>
                                    <li v-else>
                                        <span class="material-icons red" >close</span>
                                        noch nicht abgestimmt
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
               </span>
            </div>
        </div>
        
        <!-- Choose a Votingweight -->
        <div id="listOfVotings" class="voting-list" v-if="votingweight == null && topic.votingweights.length > 0">
            <div>Bitte wählen Sie Ihren Verein aus, für den sie abstimmen wollen:</div>

            <div class="list-group mt-4" >
                <div class="list-group-item list-group-item-action p-3 mr-4 mt-2 border" v-for="vw in topic.votingweights" :key="vw.id" >
                    <div>{{vw.name}}</div>
                    <a href="" class="stretched-link" v-on:click.prevent="setVotingweight(vw)"></a>
                </div>
            </div>

            <br/><br/>

        </div>        
        
        <br/><br/>
    </div>
</template>

<script type="text/javascript">

var votingTopicComponent = {
    template: '#voting-topic-component',
    props: ['topic','votingweight'],
    components: {
    },
    data: function(){
        return {
            config: {
                topNoVotings: 'Zur Zeit sind keine Abstimmungen vorhanden.',
                topErrorLoading: false,
                topErrorLoadingMessage: 'Daten konnten nicht geladen werden.',
                getLink: '<?= Url::toRoute(['voting/get-questions-of-topic']).'/' ?>',
                getVWAnswersLink: '<?= Url::toRoute(['voting/get-answers-of-votingweight']).'/' ?>'
            },
            items: [
            ]
        }
    },
    mounted: function() {
        if( this.votingweight == null && this.topic.votingweights.length>0 ){
        }
        else {
            this.getItems()
        }
    },
    watch: {
        topic: function(newVal, oldVal) { 
            this.getItems()
        },
        votingweight: function(newVal, oldVal) { 
            this.getItems()
        }
    },
    methods: {
        setVotingweight(vw) {
            this.$parent.setVotingweight(vw)
        },
        getItems() {
            //item laden
            self = this;
            var url = self.config.getLink + self.topic.id
            if(self.votingweight != null){
               url+= '/' + self.votingweight.id
            }
            
            $.post(url,
            {
            })
            .done(function(data) {
                if(data.items){
                    self.items = data.items
                    self.config.topErrorLoading = false
                }
                else {
                    self.items = []
                    self.config.topErrorLoading = true
                }
            })
            .fail(function() {
                self.config.topErrorLoading = true
                console.log( "error loading item" )
            });
        },
        getReleasestatusOfTopic(item){
           if(item.active)
               return 'released'
           else 
               return 'notreleased'
        },
        getShowResultsCssClass(item){
           if(item.showresults == 1)
               return 'btn-light'
           else 
               return 'btn-danger'
        },
        isFirst(index){
            if(index == 0)
                return true
            else return false
        }
    }
}

</script>

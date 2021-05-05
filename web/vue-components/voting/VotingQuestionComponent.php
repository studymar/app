<?php
use yii\helpers\Url;

/**
 * 
 */
/* vue laden */
use app\assets\FormAsset;
FormAsset::register($this);
use app\assets\VueAsset;
VueAsset::register($this);

/*
if(YII_ENV_TEST)
    $env_vuecomponent_dir = __DIR__.'/../../web/vue-components/voting/';
else $env_vuecomponent_dir = "vue-components/voting/";
*/
//include_once($env_vuecomponent_dir.'TeaserlistSortComponent.php');

?>

<template id="voting-question-component">
    <div id="votingQuestionComponent" class="p-2">
        
        <div class="d-flex justify-content-between title-block">
            <h3 class="">
                <a href="" class="onlySmall" v-on:click.prevent="$parent.closeVote()">
                    <i class="material-icons">keyboard_arrow_left</i>
                </a>
                {{topic.headline}}
            </h3>
            <br/>
        </div>

        <div>
            <p class="alert alert-danger" v-if="config.topErrorSaving">{{config.topErrorSavingMessage}}</p>

            <!-- Choose a Votingweight -->
            <validation-observer v-slot="{ handleSubmit }">
            <form v-on:submit.prevent="handleSubmit(onSubmit)" method="POST">

            <div id="listOfVotings" class="voting-list" v-if="question != null">
                
                <div class="fs-4 mt-4">
                    Frage:<br/>
                    {{question.question}}
                </div>

                
                <div class="list-group mt-4" v-if="question.votingoptions.length > 0 && question.votingtype.name == 'checkbox'">
                    <div class="font-italic">(Mehrere Antworten möglich)</div>
                    <validation-provider rules="required" v-slot="{ errors }" name="Antwort">
                    <div class="list-group-item list-group-item-action p-3 mr-4 mt-2 border" v-for="option in question.votingoptions" :key="option.id" >
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" v-bind:id="'answer-' + option.id" v-model="votinganswers" v-bind:value="option.id">
                                <label class="form-check-label stretched-link" v-bind:for="'answer-' + option.id">{{option.value}}</label>
                            </div>
                    </div>
                    <span class="invalid small">{{ errors[0] }}</span>
                    </validation-provider>
                </div>
                
                <div class="list-group mt-4" v-if="question.votingoptions.length > 0 && question.votingtype.name == 'radio'">
                    <validation-provider rules="required" v-slot="{ errors }" name="Antwort">
                    <div class="list-group-item list-group-item-action p-3 mr-4 mt-2 border" v-for="option in question.votingoptions" :key="option.id" >
                            <div class="form-check">
                                <input class="form-check-input" type="radio" v-bind:id="'answer-' + option.id" name="answer" v-model="votinganswers" v-bind:value="option.id">
                                <label class="form-check-label stretched-link" v-bind:for="'answer-' + option.id">{{option.value}}</label>
                            </div>
                    </div>
                    <span class="invalid small">{{ errors[0] }}</span>
                    </validation-provider>
                </div>

                <div class="list-group mt-4" v-if="question.votingtype.name == 'text'">
                    <validation-provider rules="required" v-slot="{ errors }" name="Antwort">
                    <div class="list-group-item p-3 mr-4 mt-2 border" >
                        <label class="form-label" for="answer-text">Antwort:</label>
                        <input class="form-control" type="text" id="answer-text" name="answer" v-model="votinganswers">
                    </div>
                    <span class="invalid small">{{ errors[0] }}</span>
                    </validation-provider>
                </div>
                
                
                <br/>
                <button type="submit" id="submit-button" class="btn btn-primary">Speichern</button>
                <a href="" v-on:click.prevent="$parent.closeVote()" class="btn btn-outline-primary" id="cancel-button">Zurück</a>

                <br/><br/>

            </div>        

            </form>
            </validation-observer>
        
            <br/><br/>
    </div>
</template>

<script type="text/javascript">
Vue.component('validation-observer', VeeValidate.ValidationObserver);

var votingQuestionComponent = {
    template: '#voting-question-component',
    props: ['question', 'topic', 'votingweight'],
    components: {
    },
    data: function(){
        return {
            config: {
                topErrorSaving: false,
                topErrorSavingMessage: 'Abstimmung konnte nicht gespeichert werden.',
                saveLink: '<?= Url::toRoute(['voting/save-voting-of-question']).'/' ?>',
            },
            items: [
            ],
            errors: [],
            votinganswers: []
        }
    },
    mounted: function() {
    },
    watch: {
    },
    methods: {
        onSubmit() {
            //item laden
            self = this;

            $.post(self.config.saveLink + self.question.id,
            {
                'VoteForm[votinganswer]': self.votinganswers,
                'VoteForm[votingweights_id]': (self.votingweight)? self.votingweight.id: null
            })
            .done(function(data) {
                if(data.saved){
                    self.config.topErrorSaving = false
                    self.$parent.closeVote()
                }
                else {
                    self.items = []
                    self.config.topErrorSaving = true
                }
            })
            .fail(function() {
                self.config.topErrorSaving = true
                console.log( "error saving item" )
            });
        }
    }
}

</script>

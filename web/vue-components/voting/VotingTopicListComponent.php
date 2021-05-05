<?php
use yii\helpers\Url;

/**
 * Liste der Veranstaltungen
 */

/* vue laden */
use app\assets\VueAsset;
VueAsset::register($this);

?>

<template id="votingtopiclist-component">
    
                <div class=" voting-topic-list p-2">
                    <div class="d-flex justify-content-between title-block mb-1">
                        <h3 class="m-1 onlySmall">Bitte wählen sie eine Veranstaltung aus:</h3>
                        <h3 class="m-1 notOnSmall">Veranstaltungen:</h3>
                        <br/>
                    </div>
                    
                    
                    <div v-for="(item,index) in items" :key="item.id" class="card" v-bind:class="{ 'active': topic!=null && item.id==topic.id}">
                        <div class="card-body">
                            <h5 class="card-title">{{item.headline}}</h5>
                            <a href="#" v-on:click.prevent="$parent.getTopic(item)" class="stretched-link"></a>
                        </div>
                    </div>
                    <div v-if="items.length == 0" class="alert alert-secondary">
                        {{config.topNoVotingTopics}}
                    </div>
                </div>
    
</template>

<script type="text/javascript">

var votingTopicListComponent = {
    template: '#votingtopiclist-component',
    props: ['topic'],
    components: {
    },
    data: function(){
        return {
            config: {
                topNoVotingTopics: 'Zur Zeit sind keine Veranstaltungen aktiv.',
                topErrorLoading: false,
                topErrorLoadingMessage: 'Daten konnten nicht geladen werden.',
                getLink: '/voting/get-topics/'
            },
            items: [
            ]
        }
    },
    mounted: function() {
        this.getItems()
    },
    methods: {
        getItems() {
            //item laden
            self = this;
            $.post(self.config.getLink,
            {
            })
            .done(function(data) {
                self.items = data
                self.config.topErrorLoading = false
            })
            .fail(function() {
                self.config.topErrorLoading = true
                console.log( "error loading item" )
            });
        }
    }
}

</script>

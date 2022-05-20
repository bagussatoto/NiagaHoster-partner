<?php
get_header(); ?>

<div id="vue-app">
    <div v-if="flow_step != 3" class="nipa-container">
        <website-instant v-if="flow_step == 1" :class="{'display-none': !website_selected}"></website-instant>

        <div class="md-flex" :class="{'display-none': website_selected}">
            <div class="left-side">
                <choose-domain v-if="flow_step == 1"></choose-domain>
                <client-information v-if="flow_step == 2"></client-information>
            </div>
            <div class="right-side">
                <cart-summary></cart-summary>
            </div>
        </div>
    </div>

    <div v-if="flow_step == 3" class="nipa-container">
        <checkout></checkout>
    </div>
    <loading></loading>
    <popup-message></popup-message>
    <div class="nipa-space"></div>
</div>

<?php
include_once NIPA_PLUGIN_PATH . 'templates/front/page-script.php';
get_footer();

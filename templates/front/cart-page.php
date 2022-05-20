<?php
get_header(); ?>

<div id="vue-app">
    <div v-if="flow_step != 3" class="nipa-container  md-flex">
        <div class="left-side">
            <cart></cart>
            <client-information></client-information>
        </div>
        <div class="right-side">
            <cart-summary></cart-summary>
        </div>
    </div>
    <div v-if="flow_step == 3" class="nipa-container">
        <checkout></checkout>
    </div>
    <loading></loading>
</div>

<?php
include_once NIPA_PLUGIN_PATH . 'templates/front/page-script.php';
get_footer();

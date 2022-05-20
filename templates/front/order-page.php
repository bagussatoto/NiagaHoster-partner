<?php
get_header(); ?>

<div id="vue-app">
    <div v-if="flow_step != 3" class="nipa-container  md-flex">
        <div class="left-side">
            <div v-if="flow_step == 1">
                <hosting-package v-if="flow_type != 'domain'"></hosting-package>
                <div v-if="flow_type != 'vpsme'">
                    <choose-domain></choose-domain>
                    <hosting-package-optional v-if="flow_type == 'domain'"></hosting-package-optional>
                    <ssl-package></ssl-package>
                </div>
                <div v-else-if="flow_type == 'vpsme'">
                    <hostname-template></hostname-template>
                    <license-package></license-package>
                </div>
            </div>
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
    <popup-message></popup-message>
    <div class="nipa-space"></div>
</div>

<?php
include_once NIPA_PLUGIN_PATH . 'templates/front/page-script.php';
get_footer();

<div class="wrap">
    <?php settings_errors() ?>
    <div class="nipa-admin">
        <form method="post" action="options.php">
            <?php
            settings_fields('nipa_settings');
            do_settings_sections('nipa');
            ?>
            <div class="nipa-admin__button-wrapper  nipa-admin__button-wrapper--inline">
                <button type="submit" name="submit" id="submit" class="nipa-admin__button  button button-primary">Save Changes</button>
                <button type="button" class="nipa__admin-button button button-info" id="nipa-jump">Login Reseller</button>
                <button type="button" class="nipa__admin-button button button-info" id="nipa-wa-support">
                    <span class="wa-icon"></span>
                    <span class="wa-text">WA Support</span>
                </button>
            </div>
        </form>
    </div>
</div>
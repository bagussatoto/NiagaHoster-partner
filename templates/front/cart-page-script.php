<script>
    window.app = new Vue({
        el: "#vue-app",
        computed: {
            flow_step() {
                return this.$store.state.app.flow_step;
            }
        },
        data() {
            return {
                hei: 'you'
            }
        },

        methods: {

        },

        mounted: function() {

        },

        created() {

        }
    });
</script>
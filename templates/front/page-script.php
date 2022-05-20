<?php
    global $typeOrder;
    $nipaOption = json_encode(get_option( 'nipa' ));
?>
<script>
  window.app = new Vue({
    el: "#vue-app",
    computed: {
      flow_step() {
        return this.$store.state.app.flow_step;
      },
      website_selected() {
        return this.$store.state.product.website_selected;
      },
      flow_type() {
        let type = '<?php echo $typeOrder ?>';
        if (type == 'hosting') {
          let urlParams = new URLSearchParams(window.location.search);
          if (urlParams.has('type') && urlParams.get('type') in this.$vars.product) {
            type = urlParams.get('type');
          }
        }
        return type;
      }
    },
    methods: {
      setFlowType() {
        this.$store.commit('app/setFlowType', {
          flow: this.flow_type
        });
      },
      setTitle() {
        let type        = this.flow_type;
        let option      = JSON.parse('<?php echo $nipaOption ?>');
        let customTitle = 'title_' + type.replace('-', '_');
        let title       = option[customTitle] || this.$vars.product[type].title;
        let domainTitle = option['title_domain'] || this.$vars.product['domain'].title;

        if (type == 'domain') {
          this.$store.commit('product/setHostingOptionalTitle', {
            title: option['title_hosting'] || this.$vars.product['hosting'].title,
          });

          domainTitle = title;
        }

        this.$store.commit('product/setProduct', {
          title: title,
          type: this.$vars.product[type].type
        });
        this.$store.commit('product/setDomainTitle', {
          title: domainTitle,
        });
      },
      setProduct() {
        this.$store.dispatch('product/items');
        if (this.flow_type == 'website') {
          this.$store.commit("product/setStatusWebsite", true);
          this.$store.dispatch('product/websiteItems');
        }
      },
      memberAreaButtonListener() {
        this.$store.commit("app/showLoading");
        axios
          .post(`${nipa.ajax_url}/nipa/client-dashboard`)
          .then(res => {
            this.$store.commit("app/hideLoading");
            window.open(res.data.data.url, "_blank");
          })
          .catch(err => {
            console.log(err);
          });
      },
      logoutButtonListener() {
        this.$store.commit("app/showLoading");
        axios
          .post(`${nipa.ajax_url}/nipa/client-logout`)
          .then(res => {
            this.$store.commit("app/hideLoading");
            if (res.data.success) {
              window.location.reload();
            }
          })
          .catch(err => {
            console.log(err);
          });
      },
      setClientIfLoggedIn() {
        this.$store.commit("app/showLoading");
        axios
          .get(`${nipa.ajax_url}/nipa/client`)
          .then(res => {
            let client = res.data.data;
            let loggedIn = false;
            if (typeof client.id !== 'undefined') {
              loggedIn = true;
              this.$store.commit('client/set', client);
            }
            this.$store.commit('app/loggedIn', loggedIn);
            this.$store.commit("app/hideLoading");
          })
          .catch(err => {
            console.log(err);
          });
      }
    },
    mounted: function() {
      this.setProduct();
      this.setClientIfLoggedIn();
      this.setFlowType();
      this.setTitle();
    },
    created() {
      let dashboard = document.getElementsByClassName("nipa-client-dashboard");
      if (dashboard != null) {
        for (var i = 0; i < dashboard.length; i++) {
          dashboard[i].addEventListener('click', this.memberAreaButtonListener);
        }
      }
      let logout = document.getElementsByClassName('nipa-client-logout');
      if (logout != null) {
        for (var i = 0; i < dashboard.length; i++) {
          logout[i].addEventListener('click', this.logoutButtonListener);
        }
      }
    },
  });
</script>
import Vue from 'vue';
import Vuex from 'vuex';
import store from './store';

//components
import App from './App.vue';

Vue.use(Vuex);

new Vue({
	el: '#app',
	render: h => h(App),
	store: new Vuex.Store(store),
	created() {
		this.$store.dispatch('onLoad');
	}
})

<template>
    <div>
		<modal></modal>
        <div id="wrapper">

			<div class="left-side-menu">
                <div class="h-100" data-simplebar> 
						<div id="sidebar-menu" class="slimscroll-menu">
							<ul id="side-menu">
										<li>
											<a onclick="window.open(window.location.origin)">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
												<span> Back </span>
											</a>
										</li>
									   <li class="menu-title mt-2">Control</li>
										<li>
											<router-link to="/admin">
												<feather type="activity"></feather>
												<span> Dashboard</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/wallet">
												<feather type="clock"></feather>
												<span class="badge bg-danger float-end" v-if="info">
													{{ info.withdraws }}
												</span>
												<span> Withdraws</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/promo">
												<feather type="percent"></feather>
												<span> Promocodes</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/challenges">
												<feather type="trello"></feather>
												<span> Challenges</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/users">
												<feather type="users"></feather>
												<span> Users</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/notifications">
												<feather type="bell"></feather>
												<span> Notifications</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/currency">
												<feather type="disc"></feather>
												<span> Currency</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/activity">
												<feather type="alert-triangle"></feather>
												<span> Activity</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/modules">
												<feather type="git-merge"></feather>
												<span> Inhouse Games</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/externalgames">
												<feather type="zap"></feather>
												<span> External Games</span>
											</router-link>
										</li>                            
										<li>
											<router-link to="/admin/bot">
												<feather type="shuffle"></feather>
												<span> Bot Settings</span>
											</router-link>
										</li>
										<li>
											<router-link to="/admin/settings">
												<feather type="settings"></feather>
												<span> Settings</span>
											</router-link>
										</li>
										<li class="menu-title">Server</li>
										<li>
											<a href="javascript:void(0)" onclick="window.open('/admin/logs', '_blank');">
												<feather type="align-left"></feather>
												<span class="badge bg-danger float-end" style="background: #fd0c31" v-if="info">
													{{ info.logs.critical }}
												</span>
												<span class="badge bg-danger float-end" style="position: relative; right: 5px" v-if="info">
													{{ info.logs.error }}
												</span>
												<span> Logs</span>
											</a>
										</li> 
										<li>
											<a href="javascript:void(0)">
												<feather type="hash"></feather>
												<span class="badge bg-success float-end" v-if="info">
													{{ info.version }}
												</span>
												<span> Version</span>
											</a>
										</li>
							</ul>
						</div>
                    <div class="clearfix"></div>
                </div>
            </div>



            <div class="content-page">
             <loader v-if="!loadedContent"></loader>
                    <template v-else>
                 <div class="content">
                    <router-view :key="$route.fullPath" class="container-fluid"></router-view>
                </div>
                 </template>

                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                {{ new Date().getFullYear() }} &copy;
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

    </div>
</template>

<script>
	let el = document.getElementsByTagName('body')[0];
    import { mapGetters } from 'vuex';

    export default {
        data() {
            return {
            	loadedContent: null,
                info: null,
				mobileMenu: false
            }
        },
        computed: {
            ...mapGetters(['games'])
        },
        mounted() {
            axios.post('/admin/info').then(({ data }) => this.info = data);
        },
        created() {
        	this.loadedContent = true;
        },
        methods: {
            toggleGame(id) {
                axios.post('/admin/toggle', { name: id });
                this.$store.dispatch('updateData');
            },
            toggleRightBar() {
                const active = $('.rightbar-overlay').hasClass('active');
                $('.rightbar-overlay').toggle().toggleClass('active');
                $('.right-bar').css({'right': active ? '-270px' : 0});
            },
			toggleBarMobile() {
				this.mobileMenu = !this.mobileMenu;
				this.mobileMenu ? (el.classList.add('sidebar-enable')) : (el.classList.remove('sidebar-enable')); 
			}
        }
    }
</script>

<style>

.left-side-menu {
	overflow: scroll;
	z-index: 12 !important;
}

.left-side-menu::-webkit-scrollbar {
  width: 1mm;
}
 
.left-side-menu::-webkit-scrollbar-track {
  box-shadow: inset 0 0 6px rgb(61 66 77);
}
 
.left-side-menu::-webkit-scrollbar-thumb {
  background-color: darkgrey;
  outline: 1px solid slategrey;
}

</style>
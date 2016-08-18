<?php
session_start();
if(!isset($_SESSION['user_id'])){
	header('Location: /til/login.html');
}
?>
<html lang="en" ng-app="TILApp">

  <head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Angular Material Dependencies -->
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.6/angular.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.6/angular-animate.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.5/angular-aria.min.js"></script>

    <script src="//ajax.googleapis.com/ajax/libs/angular_material/0.8.3/angular-material.min.js"></script>
    
    <script src="//cdn.jsdelivr.net/angular-material-icons/0.4.0/angular-material-icons.min.js"></script> 
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/angular_material/0.8.3/angular-material.min.css">
	<style>
	md-content.md-default-theme {
	  background-color: #eee;
	}

	md-card {
	  background-color: #fff;
	}
	md-card h2:first-of-type {
	  margin-top: 0;
	}

	md-toolbar .md-button.md-default-theme {
	  border-radius: 99%;
	}

	h2 {
	  font-weight: 400;
	}

	.md-toolbar-tools-bottom {
	  font-size: small;
	}
	.md-toolbar-tools-bottom :last-child {
	  opacity: 0.8;
	}

	md-toolbar:not(.md-hue-1),
	.md-fab {
	  fill: #fff;
	}

	md-sidenav {
	  fill: #737373;
	}
	md-sidenav ng-md-icon {
	  position: relative;
	  top: 5px;
	}

	.user-avatar {
	  border-radius: 99%;
	}
	.add-dialog{
		width: 70%;
	}
	
	.add-input-box{
		height: 100px;
	}
	
	.menu-icon{
		padding-top: 0px;
	}
	</style>
	<script>
	var app = angular.module('TILApp', ['ngMaterial', 'ngMdIcons']);

	app.controller('AppCtrl', ['$scope', '$mdBottomSheet','$mdSidenav', '$mdDialog', '$http', function($scope, $mdBottomSheet, $mdSidenav, $mdDialog, $http){
	  $scope.toggleSidenav = function(menuId) {
		$mdSidenav(menuId).toggle();
	  };
		$scope.menu = [
		{
		  link : '/til/index.php',
		  title: 'Home',
		  icon: 'home'
		},
		{
		  link : '/til/logout.php',
		  title: 'Logout',
		  icon: 'close'
		}
	  ];
	  $scope.alert = '';
	  
	  $scope.showAdd = function(ev) {
		$mdDialog.show({
		  controller: 'DialogController',
		  template: '<md-dialog aria-label="add entry" class="add-dialog"> <md-content class="md-padding"> <form name="userForm"> <div layout layout-sm="column" class="add-input-box"> <md-input-container flex> <label>Today I Learned</label> <input ng-model="newentry"> </md-input-container></div>  </form> </md-content> <div class="md-actions" layout="row"> <span flex></span> <md-button ng-click="cancel()"> Cancel </md-button> <md-button ng-click="addentry()" class="md-primary"> Save </md-button> </div></md-dialog>',
		  targetEvent: ev,
		});
	  };
	  $scope.getRecentEntries = function(){
			var req = {};
			req.operation = "get_recent_entries";
			$http({
				method: 'POST',
				url: 'til.php',
				data: req,
				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			})
			.then(function (result) {
				$scope.entries = JSON.parse(result.data.response);
			});
		}
		
		$scope.$on('entryAdded', function(event, args){
			$scope.getRecentEntries();
		});
				
		$scope.getRecentEntries();
	}]);
	
	app.controller('DialogController', function($scope, $mdDialog, $http, $rootScope){
		$scope.hide = function() {
		$mdDialog.hide();
	  };
	  $scope.cancel = function() {
		$mdDialog.cancel();
	  };
		$scope.addentry = function(){
			var val = $scope.newentry;
			var req = {};
			req.operation = "add_entry";
			req.payload = val;
			$http({
				method: 'POST',
				url: 'til.php',
				data: req,
				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			})
			.then(function (result) {
				$scope.newentry = '';
				$rootScope.$broadcast('entryAdded');
				$mdDialog.hide();
				
			});	
		}
	});

	app.directive('userAvatar', function() {
	  return {
		replace: true,
		template: '<svg class="user-avatar" viewBox="0 0 128 128" height="64" width="64" pointer-events="none" display="block" > <path fill="#FF8A80" d="M0 0h128v128H0z"/> <path fill="#FFE0B2" d="M36.3 94.8c6.4 7.3 16.2 12.1 27.3 12.4 10.7-.3 20.3-4.7 26.7-11.6l.2.1c-17-13.3-12.9-23.4-8.5-28.6 1.3-1.2 2.8-2.5 4.4-3.9l13.1-11c1.5-1.2 2.6-3 2.9-5.1.6-4.4-2.5-8.4-6.9-9.1-1.5-.2-3 0-4.3.6-.3-1.3-.4-2.7-1.6-3.5-1.4-.9-2.8-1.7-4.2-2.5-7.1-3.9-14.9-6.6-23-7.9-5.4-.9-11-1.2-16.1.7-3.3 1.2-6.1 3.2-8.7 5.6-1.3 1.2-2.5 2.4-3.7 3.7l-1.8 1.9c-.3.3-.5.6-.8.8-.1.1-.2 0-.4.2.1.2.1.5.1.6-1-.3-2.1-.4-3.2-.2-4.4.6-7.5 4.7-6.9 9.1.3 2.1 1.3 3.8 2.8 5.1l11 9.3c1.8 1.5 3.3 3.8 4.6 5.7 1.5 2.3 2.8 4.9 3.5 7.6 1.7 6.8-.8 13.4-5.4 18.4-.5.6-1.1 1-1.4 1.7-.2.6-.4 1.3-.6 2-.4 1.5-.5 3.1-.3 4.6.4 3.1 1.8 6.1 4.1 8.2 3.3 3 8 4 12.4 4.5 5.2.6 10.5.7 15.7.2 4.5-.4 9.1-1.2 13-3.4 5.6-3.1 9.6-8.9 10.5-15.2M76.4 46c.9 0 1.6.7 1.6 1.6 0 .9-.7 1.6-1.6 1.6-.9 0-1.6-.7-1.6-1.6-.1-.9.7-1.6 1.6-1.6zm-25.7 0c.9 0 1.6.7 1.6 1.6 0 .9-.7 1.6-1.6 1.6-.9 0-1.6-.7-1.6-1.6-.1-.9.7-1.6 1.6-1.6z"/> <path fill="#E0F7FA" d="M105.3 106.1c-.9-1.3-1.3-1.9-1.3-1.9l-.2-.3c-.6-.9-1.2-1.7-1.9-2.4-3.2-3.5-7.3-5.4-11.4-5.7 0 0 .1 0 .1.1l-.2-.1c-6.4 6.9-16 11.3-26.7 11.6-11.2-.3-21.1-5.1-27.5-12.6-.1.2-.2.4-.2.5-3.1.9-6 2.7-8.4 5.4l-.2.2s-.5.6-1.5 1.7c-.9 1.1-2.2 2.6-3.7 4.5-3.1 3.9-7.2 9.5-11.7 16.6-.9 1.4-1.7 2.8-2.6 4.3h109.6c-3.4-7.1-6.5-12.8-8.9-16.9-1.5-2.2-2.6-3.8-3.3-5z"/> <circle fill="#444" cx="76.3" cy="47.5" r="2"/> <circle fill="#444" cx="50.7" cy="47.6" r="2"/> <path fill="#444" d="M48.1 27.4c4.5 5.9 15.5 12.1 42.4 8.4-2.2-6.9-6.8-12.6-12.6-16.4C95.1 20.9 92 10 92 10c-1.4 5.5-11.1 4.4-11.1 4.4H62.1c-1.7-.1-3.4 0-5.2.3-12.8 1.8-22.6 11.1-25.7 22.9 10.6-1.9 15.3-7.6 16.9-10.2z"/> </svg>'
	  };
	});

	app.config(function($mdThemingProvider) {
	  var customBlueMap = 		$mdThemingProvider.extendPalette('light-blue', {
		'contrastDefaultColor': 'light',
		'contrastDarkColors': ['50'],
		'50': 'ffffff'
	  });
	  $mdThemingProvider.definePalette('customBlue', customBlueMap);
	  $mdThemingProvider.theme('default')
		.primaryPalette('customBlue', {
		  'default': '500',
		  'hue-1': '50'
		})
		.accentPalette('pink');
	  $mdThemingProvider.theme('input', 'default')
			.primaryPalette('grey')
	});
	</script>
  </head>
  <body layout="row" ng-controller="AppCtrl">
    <md-sidenav layout="column" class="md-sidenav-left md-whiteframe-z2" md-component-id="left" md-is-locked-open="$mdMedia('gt-md')">
      <md-toolbar class="md-tall md-hue-2">
        <span flex></span>
        <div layout="column" class="md-toolbar-tools-bottom inset">
          <user-avatar></user-avatar>
          <span></span>
          <div>Welcome <?php echo $_SESSION['user_name'] .'!'?></div>
        </div>
      </md-toolbar>
      <md-list>
      <md-item ng-repeat="item in menu">
        <md-button ng-href="{{item.link}}">
          <md-item-content md-ink-ripple layout="row" layout-align="start center">
            <div class="inset menu-icon">
              <ng-md-icon icon="{{item.icon}}"></ng-md-icon>
            </div>
            <div class="inset">{{item.title}}
            </div>
          </md-item-content>
        </md-button>
      </md-item>
      <md-divider></md-divider>
    </md-list>
    </md-sidenav>
    <div layout="column" class="relative" layout-fill role="main">
      <md-button class="md-fab md-fab-bottom-right" aria-label="Add" ng-click="showAdd($event)">
        <ng-md-icon icon="add"></ng-md-icon>
      </md-button>
      <md-toolbar ng-show="!showSearch">
        <div class="md-toolbar-tools">
          <md-button ng-click="toggleSidenav('left')" hide-gt-md aria-label="Menu">
            <ng-md-icon icon="menu"></ng-md-icon>
          </md-button>
          <h3>
            Dashboard
          </h3>
          <span flex></span>
          <md-button aria-label="Search" ng-click="showSearch = !showSearch">
            <ng-md-icon icon="search"></ng-md-icon>
          </md-button>
        </div>
        <md-tabs md-stretch-tabs class="md-primary" md-selected="data.selectedIndex">
          <md-tab id="tab1" aria-controls="tab1-content">
            Latest
          </md-tab>
        </md-tabs>
      </md-toolbar>
      <md-toolbar class="md-hue-1" ng-show="showSearch">
        <div class="md-toolbar-tools">
          <md-button ng-click="showSearch = !showSearch; searchtext=''" aria-label="Back">
            <ng-md-icon icon="arrow_back"></ng-md-icon>
          </md-button>
          <h3 flex="10">
            Back
          </h3>
          <md-input-container md-theme="input" flex>
            <label>&nbsp;</label>
            <input ng-model="searchtext" placeholder="enter search">
          </md-input-container>
          <md-button aria-label="Search" ng-click="showSearch = !showSearch">
            <ng-md-icon icon="search"></ng-md-icon>
          </md-button>
        </div>
       
      </md-toolbar>
      <md-content flex md-scroll-y>
        <ui-view layout="column" layout-fill layout-padding>
          <div class="inset" hide-sm></div>
            <ng-switch on="data.selectedIndex" class="tabpanel-container">
              <div role="tabpanel"
                   id="tab1-content"
                   aria-labelledby="tab1"
                   ng-switch-when="0"
                   md-swipe-left="next()"
                   md-swipe-right="previous()"
                   layout="row" layout-align="center center">
                  <md-card flex-gt-sm="90" flex-gt-md="80">
                    <md-card-content>
                      <h2>Entries</h2>
                      <md-list>
                        <md-item ng-repeat="item in entries | filter:searchtext">
                          <md-item-content>
                            <div class="md-tile-left inset" hide-sm>
                                <user-avatar></user-avatar>
                            </div>
                            <div class="md-tile-content">
                              <h3>{{ item.date.sec *1000 | date:'MM/dd/yyyy'}}</h3>
                              <p>
                                {{item.text}}
                              </p>
                            </div>
                          </md-item-content>
                          <md-divider md-inset hide-sm ng-if="!$last"></md-divider>
                          <md-divider hide-gt-sm ng-if="!$last"></md-divider>
                        </md-item>
                        <md-divider></md-divider>
                        <md-item layout class="inset">
                            <md-button layout layout-align="start center" flex class="md-primary">
                              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg> More
                            </md-button>
                        </md-item>
                      </md-list>
                    </md-card-content>
                  </md-card>
              </div>
              
          </ng-switch>
          
        </ui-view>
      </md-content>
    </div>
	
    
  </body>
</html>
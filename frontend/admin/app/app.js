angular.module('adminApp', [])
.run(function($rootScope, $http, $window){
  $rootScope.api = function(path){ return '/backend/api' + path; };

  // Global logout available on all admin pages (works even if AuthCtrl isn't instantiated)
  $rootScope.logout = function(){
    $http.get($rootScope.api('/auth/logout.php')).finally(function(){
      $window.location.href = '/';
    });
  };
});

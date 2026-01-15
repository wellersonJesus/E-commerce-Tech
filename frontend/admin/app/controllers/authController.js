angular.module('adminApp')
.controller('AuthCtrl', function($scope, $http, $window, $rootScope){
  $scope.email = '';
  $scope.password = '';
  $scope.error = null;
  $scope.login = function(){
    $http.post($rootScope.api('/auth/login.php'), {email:$scope.email, password:$scope.password})
      .then(function(resp){
        $window.location.href = '/frontend/admin/dashboard.html';
      }, function(err){
        $scope.error = err.data && err.data.error ? err.data.error : 'Erro';
      });
  };
  // auto-fill from query string
  try {
    var params = new URLSearchParams(window.location.search);
    var e = params.get('email');
    var p = params.get('password');
    if (e) $scope.email = decodeURIComponent(e);
    if (p) $scope.password = decodeURIComponent(p);
    if (e && p) setTimeout(function(){ $scope.login(); }, 200);
  } catch (ex) {}
  // expose logout for other controllers
  $rootScope.logout = function(){
    $http.get($rootScope.api('/auth/logout.php')).finally(function(){
      $window.location.href = '/';
    });
  };
});

angular.module('adminApp')
.controller('DashboardCtrl', function($scope, $http, $rootScope){
  $scope.user = {name: 'Carregando...'};
  $scope.metrics = {users:0, services:0};

  $http.get($rootScope.api('/dashboard/metrics.php')).then(function(resp){
    $scope.metrics = resp.data;
  }, function(){
    window.location.href = '/frontend/admin/login.html';
  });

  // Carregar dados do usuário logado
  $http.get($rootScope.api('/auth/me.php')).then(function(resp){
    $scope.user = resp.data;
  }, function(){
    window.location.href = '/frontend/admin/login.html';
  });

  if ($rootScope && typeof $rootScope.logout === 'function') {
    $scope.logout = $rootScope.logout;
  }
});

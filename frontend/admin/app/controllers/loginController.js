angular.module('adminApp').controller('LoginCtrl', function($scope, $http, $rootScope) {
    $scope.user = {};
    $scope.error = '';

    $scope.login = function() {
        $scope.error = '';
        // Envia os dados para o endpoint de login
        $http.post($rootScope.api('/auth/login.php'), $scope.user).then(function(resp) {
            // Se o login for bem-sucedido
            if (resp.data.success) {
                // Verifica a 'role' do usuário retornada pelo backend
                if (resp.data.role === 'admin') {
                    // Se for 'admin', redireciona para o dashboard administrativo
                    window.location.href = '/frontend/admin/dashboard.html';
                } else {
                    // Se for um usuário comum, redireciona para o painel do cliente
                    window.location.href = '/frontend/client/dashboard.html';
                }
            }
        }, function(err) {
            $scope.error = err.data.error || 'Falha na autenticação.';
        });
    };
});
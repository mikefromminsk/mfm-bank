function openShare(domain, success) {
    showBottomSheet("/mfm-bank/share/create/index.html?nocache", success, function ($scope) {
        domain = domain || wallet.gas_domain
        $scope.domain = domain

        $scope.create = function () {
            getPin(function (pin) {
                calcPass(domain, pin, function (pass) {
                    $scope.in_progress = true
                    let invite_code = randomString(8)
                    postContractWithGas("mfm-bank", "share.php", {
                        domain: domain,
                        pass: pass,
                        amount: $scope.amount,
                        invite_next_hash: CryptoJS.MD5(invite_code),
                    }, function () {
                        $scope.false = true
                        $scope.link = location.origin + "/mfm-wallet/?bonus=" + domain + ":" + invite_code
                        $scope.$apply()
                    }, function (message) {
                        $scope.false = true
                        showError(message)
                    })
                })
            })
        }

        $scope.copyUrl = function () {
            $scope.copy($scope.link)
        }

        $scope.setMax = function () {
            $scope.amount = $scope.token.balance
        }

        function init() {
            getProfile(domain, function (response) {
                $scope.token = response
                $scope.$apply()
            })
        }

        init()
    })
}
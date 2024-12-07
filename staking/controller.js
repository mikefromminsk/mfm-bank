function openStaking(domain, success) {
    trackCall(arguments)
    showDialog('/mfm-bank/staking/index.html?nocache', success, function ($scope) {
        $scope.domain = domain

        $scope.stake = function () {
            postContract("mfm-bank", "stake.php", {
                domain: wallet.gas_domain,
                address: wallet.address(),
                amount: $scope.amount,
            }, function () {
                showSuccessDialog(str.you_have_unstaked + " " + $scope.formatAmount($scope.amount, wallet.gas_domain))
            })
        }

        $scope.unstake = function () {
            getPin(function (pin) {
                calcPass(domain, pin, function (pass) {
                    postContract("mfm-bank", "unstake.php", {
                        domain: wallet.gas_domain,
                        address: wallet.address(),
                        pass: pass,
                    }, function (response) {
                        showSuccessDialog(str.you_have_unstaked + " " + response.unstaked)
                    })
                })
            })
        }

        function init() {
            postContract("mfm-token", "trans.php", {
                domain: wallet.gas_domain,
                from_address: wallet.address(),
                to_address: $scope.staking_address,
                size: 1
            }, function (response) {
                $scope.last_tran = response.trans[0]
                $scope.$apply()
            })
        }

        init()

    })
}
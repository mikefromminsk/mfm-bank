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
            postContract("mfm-token", "staked.php", {
                address: wallet.address(),
            }, function (response) {
                for (const stake_tran of response.staked) {
                    if (stake_tran.domain == domain) {
                        $scope.stake = stake_tran
                        break
                    }
                }
                $scope.$apply()
            })
        }

        init()

    })
}
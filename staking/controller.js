function openStaking(domain, success) {
    trackCall(arguments)
    showDialog('/mfm-bank/staking/index.html?nocache', success, function ($scope) {
        $scope.domain = domain

        $scope.stake = function () {
            openSend(domain, $scope.staking_address, $scope.profile.balance, getStakes)
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


        function getStakes(){
            postContract("mfm-bank", "staked.php", {
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

        function init() {
            getStakes()
            getProfile(domain, function (response) {
                $scope.profile = response
                $scope.$apply()
            })
        }

        init()

    })
}
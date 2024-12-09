function openStaking(domain, success) {
    trackCall(arguments)
    showDialog('/mfm-bank/staking/index.html?nocache', success, function ($scope) {
        $scope.domain = domain

        $scope.openStake = function () {
            getPin(function (pin) {
                calcPass(domain, pin, function (pass) {
                    postContract("mfm-bank", "stake.php", {
                        domain: domain,
                        amount: $scope.amount,
                        address: wallet.address(),
                        pass: pass,
                    }, function (response) {
                        showSuccessDialog(str.you_have_staked + " " + $scope.formatAmount(response.staked, domain), getStakes)
                    })
                })
            })
        }

        $scope.openUnstake = function () {
            getPin(function (pin) {
                calcPass(domain, pin, function (pass) {
                    postContract("mfm-bank", "unstake.php", {
                        domain: domain,
                        address: wallet.address(),
                        pass: pass,
                    }, function (response) {
                        showSuccessDialog(str.you_have_unstaked + " " + $scope.formatAmount(response.unstaked, domain), getStakes)
                    })
                })
            })
        }

        $scope.setMax = function () {
            $scope.amount = $scope.token.balance
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
                $scope.reward = $scope.stake.amount * $scope.stake.percent / 100 * $scope.stake.period_days / 365
                $scope.$apply()
            })
        }

        function init() {
            getStakes()
            getProfile(domain, function (response) {
                $scope.token = response
                $scope.$apply()
            })
        }

        init()

    })
}
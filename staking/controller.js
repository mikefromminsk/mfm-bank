function openStaking(domain, success) {
    trackCall(arguments)
    let rewardTimer = null
    showDialog('/mfm-bank/staking/index.html?nocache', function () {
        if (rewardTimer)
            clearInterval(rewardTimer)
        if (success)
            success()
    }, function ($scope) {
        $scope.domain = domain
        $scope.staking_address = 'staking'

        $scope.openStake = function () {
            getPin(function (pin) {
                calcPass(domain, pin, function (pass) {
                    $scope.in_progress = true
                    postContract("mfm-bank", "stake.php", {
                        domain: domain,
                        amount: $scope.amount,
                        address: wallet.address(),
                        pass: pass,
                    }, function (response) {
                        $scope.in_progress = false
                        showSuccessDialog(str.you_have_staked + " " + $scope.formatAmount(response.staked, domain), init)
                    }, function (message) {
                        $scope.in_progress = false
                        showError(message)
                    })
                })
            })
        }

        $scope.openUnstake = function () {
            getPin(function (pin) {
                calcPass(domain, pin, function (pass) {
                    $scope.in_progress = true
                    postContract("mfm-bank", "unstake.php", {
                        domain: domain,
                        address: wallet.address(),
                        pass: pass,
                    }, function (response) {
                        $scope.in_progress = false
                        showSuccessDialog(str.you_have_unstaked + " " + $scope.formatAmount(response.unstaked, domain), init)
                    }, function (message) {
                        $scope.in_progress = false
                        showError(message)
                    })
                })
            })
        }

        $scope.setMax = function () {
            $scope.amount = $scope.token.balance
        }

        function getStakes() {
            postContract("mfm-bank", "staked.php", {
                address: wallet.address(),
            }, function (response) {
                for (const stake_tran of response.staked) {
                    if (stake_tran.domain == domain) {
                        $scope.stake = stake_tran
                        if (rewardTimer == null)
                            rewardTimer = setInterval(function () {
                                let period_percent = Math.min(1, (new Date() / 1000 - stake_tran.time) / (60 * 60 * 24) / $scope.period_days)
                                $scope.reward = $scope.stake.amount * response.percent / 100 * period_percent
                                $scope.$apply()
                            }, 1000)
                        break
                    }
                }
                $scope.period_days = response.period_days
                $scope.percent = response.percent
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
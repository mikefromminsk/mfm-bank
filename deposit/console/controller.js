function openDeposit(success) {
    var depositCheckTimer
    showBottomSheet('/mfm-bank/deposit/console/index.html', function () {
        if (depositCheckTimer)
            clearInterval(depositCheckTimer)
        if (success)
            success()
    }, function ($scope) {
        $scope.chain = "TRON"

        function getDepositAddress() {
            postContract("mfm-bank", "deposit/start.php", {
                address: wallet.address(),
                chain: $scope.chain,
            }, function (response) {
                $scope.min_deposit = response.min_deposit
                $scope.deadline = response.deadline
                $scope.deposit_address = response.deposit_address
                startDepositCheckTimer()
                $scope.$apply()
            }, function (response) {
                showError(response.message)
            })
        }

        $scope.copy = function () {
            document.getElementById("deposit_address").focus();
            document.getElementById("deposit_address").select();
            document.execCommand("copy");
            showSuccess("Deposit address copied")
        }

        $scope.share = function () {
            navigator.share({
                text: $scope.deposit_address,
            })
        }

        let CHECK_INTERVAL = 10
        $scope.countDownTimer = 0
        $scope.deposited = 0

        function startDepositCheckTimer() {
            depositCheck()
            $scope.countDownTimer = CHECK_INTERVAL
            depositCheckTimer = setInterval(function () {
                $scope.countDownTimer -= 1
                $scope.$apply()
                if ($scope.countDownTimer % CHECK_INTERVAL == 0) {
                    $scope.countDownTimer = CHECK_INTERVAL
                    depositCheck()
                }
            }, 1000)
        }

        function depositCheck() {
            postContract("mfm-bank", "owner.php", {
                redirect: "mfm-bank/deposit/check.php",
                deposit_address: $scope.deposit_address,
                chain: $scope.chain,
            }, function (response) {
                if (response.deposited > 0) {
                    showSuccessDialog("You deposited " + $scope.formatAmount(response.deposited, "USDT"))
                }
            })
        }

        function init(){
            getDepositAddress()
        }

        init()
    })
}
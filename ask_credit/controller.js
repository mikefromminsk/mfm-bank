function openAskCredit(success) {
    trackCall(arguments)
    showBottomSheet('/mfm-bank/ask_credit/index.html', null, function ($scope) {
        $scope.accept = function () {
            $scope.back()
            $scope.openGetCredit(success)
        }
    })
}
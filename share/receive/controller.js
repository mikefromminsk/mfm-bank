function openShareReceive(share_pass, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/share/receive/index.html",
        controller: function ($scope) {
            addFormats($scope)


            $scope.receive = function () {
                if (wallet.address() == "") {
                    openLogin($scope.receive)
                } else {

                }
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}
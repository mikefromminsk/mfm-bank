function openCredit(success) {
    showDialog('/mfm-bank/credit/index.html', success, function ($scope) {

        $scope.pageIndex = 0

        function init() {
            get("/mfm-bank/quiz.json", function (text) {
                let levels = JSON.parse(text)
                $scope.questions = []
                for (const level of levels) {
                    $scope.questions.push(level.questions[Math.floor(Math.random() * level.questions.length)])
                }
                $scope.$apply()
            })
        }

        $scope.next = function () {
            setTimeout(function () {
                let question = $scope.questions[$scope.pageIndex]
                if (question.answer == question.correct) {
                    openTab($scope.pageIndex + 1)
                } else {
                    openTab($scope.questions.length)
                }
            }, 100)
        }

        function openTab(index) {
            setTimeout(function () {
                $scope.pageIndex = index
                $scope.$apply()
            }, 500)
        }

        $scope.$watch('pageIndex', function (newValue, oldValue) {
            if ($scope.questions != null && newValue == $scope.questions.length) {
                $scope.getRating()
            }
        })

        $scope.getRating = function () {
            postContract("mfm-bank", "rating.php", {
                address: wallet.address(),
                answers: JSON.stringify($scope.questions),
            }, function (response) {
                $scope.rating = response.rating
                $scope.percent = response.percent
                $scope.$apply()
            })
        }

        $scope.getCredit = function () {
            getPin(function (pin) {
                calcPass(wallet.gas_domain, pin, function (pass) {
                    postContract("mfm-bank", "owner.php", {
                        redirect: "mfm-bank/credit.php",
                        address: wallet.address(),
                        pass: pass,
                        answers: JSON.stringify($scope.questions),
                    }, function (response) {
                        openTab($scope.pageIndex + 1)
                    })
                })

            })
        }

        init()
    })
}
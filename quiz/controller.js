function openCredit(success) {
    showDialog('/mfm-credit/quiz/index.html', success, function ($scope) {

        $scope.pageIndex = 0
        function init(){
            get("/mfm-credit/quiz.json", function (text) {
                let levels = JSON.parse(text)
                $scope.questions = []
                for (const level of levels) {
                    $scope.questions.push(level.questions[Math.floor(Math.random() * level.questions.length)])
                    break
                }
                $scope.$apply()
            })
        }

        $scope.next = function () {
            setTimeout(function () {
                $scope.pageIndex++
                $scope.$apply()
            }, 500)
        }

        $scope.getRating = function () {
            postContract("/mfm-credit/rating.php", {
                address: wallet.address(),
                answers: $scope.questions,
            }, function (response) {
                $scope.rating = response.rating
                $scope.amount = response.rating * response.multiplier
                $scope.period = response.period
                $scope.percent = response.percent
                $scope.$apply()
            })
        }

        $scope.getCredit = function () {
            getPin(function (pin) {
                calcPass(wallet.address(), pin, function (pass) {
                    postContract("/mfm-credit/credit.php", {
                        address: wallet.address(),
                        answers: $scope.questions,
                        pass: pass
                    }, function (response) {
                        $scope.credit = response
                        $scope.$apply()
                    })
                })

            })
        }

        init()
    })
}
BX.ready(function () {
        BX.bind(BX('cities'), 'bxchange', function () {
            let cityID = this.value;
            let content = document.getElementById('ajax-content');
            BX.ajax.runComponentAction('sp:events',
                'getCity', {
                    mode: 'class',
                    signedParameters: params.signedParameters,
                    data: {cityID: cityID},
                })
                .then(function (response) {
                    content.innerHTML = response.data;
                }, function (response) {

                });
            return false
        });
    }
);
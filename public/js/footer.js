$(document).ready(function () {
    const BASE_URL = window.BASE_URL || '';

    $('#searchInput').on('input', function () {
        let keyword = $(this).val();
        if (keyword.length > 1) {
            $.ajax({
                url: BASE_URL + '/Product/autocomplete',
                method: 'GET',
                data: { keyword },
                success: function (response) {
                    $('#suggestionBox').html(response).show();
                }
            });
        } else {
            $('#suggestionBox').hide();
        }
    });

    $(document).on('click', '.suggestion-item', function () {
        const type = $(this).data('type');
        const id = $(this).data('id');

        if (type === 'product') {
            window.location.href = BASE_URL + '/Product/view/' + id;
        } else if (type === 'category') {
            window.location.href = BASE_URL + '/Product/categoryList/' + id;
        }

        $('#suggestionBox').hide();
    });
});

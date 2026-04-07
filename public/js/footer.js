
        $(document).ready(function() {
            // Khi người dùng gõ vào ô tìm kiếm
            $('#searchInput').on('input', function() {
                let keyword = $(this).val();
                if (keyword.length > 1) {
                    $.ajax({
                        url: '/webbanhang/Product/autocomplete',
                        method: 'GET',
                        data: {
                            keyword
                        },
                        success: function(response) {
                            $('#suggestionBox').html(response).show();
                        }
                    });
                } else {
                    $('#suggestionBox').hide();
                }
            });

            /// Click 1 lần → chỉ điền vào ô tìm kiếm
            $(document).on('click', '.suggestion-item', function() {
                $('#searchInput').val($(this).text());
            });

            // Click 2 lần → chuyển đến trang chi tiết
            // Click chọn gợi ý (sửa lại đoạn này)
            $(document).on('click', '.suggestion-item', function() {
                const type = $(this).data('type');
                const id = $(this).data('id');

                if (type === 'product') {
                    window.location.href = '/webbanhang/Product/view/' + id;
                } else if (type === 'category') {
                    window.location.href = '/webbanhang/Product/categoryList/' + id;
                }

                $('#suggestionBox').hide();
            });


        });


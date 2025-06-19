$(document).ready(function() {
    $('#state_id').change(function() {
        var stateId = $(this).val();
        var lgaSelect = $('#lga_id');

        console.log('State selected:', stateId);

        // Clear current LGA options
        lgaSelect.empty().append('<option value="">Select LGA...</option>');

        if (stateId) {
            $.ajax({
                url: '/get_lgas/' + stateId,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    lgaSelect.append('<option value="">Loading...</option>');
                },
                success: function(data) {
                    console.log('LGA data received:', data);
                    lgaSelect.empty().append('<option value="">Select LGA...</option>');

                    if (data && data.length > 0) {
                        $.each(data, function(key, value) {
                            lgaSelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    } else {
                        lgaSelect.append('<option value="">No LGAs found for this state</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error fetching LGAs:', error);
                    console.log('Status:', status);
                    console.log('Response:', xhr.responseText);
                    lgaSelect.empty().append('<option value="">Error loading LGAs</option>');
                }
            });
        }
    });
});

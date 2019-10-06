// Disable auto discover for all elements:
Dropzone.autoDiscover = false;
/**
 * DataTable instance.
 * @type {object}
 */
var table_pathologies = null;
/**
 * Last inserted pathologies in datatable.
 * @type {int[]}
 */
var lastInsertedIdStatistic = [];

var pathologiesDropzone = new Dropzone("#pathologies-form", {
    url: "upload.php",
    paramName: "file_pathologies", // The name that will be used to transfer the file
    maxFilesize: 20, //MB
    method: 'POST',
    acceptedFiles: 'text/csv,application/vnd.ms-excel',
    init: function () {
        $("#pathologies-form").addClass('dropzone');
    },
    sending: function () {
        /** Enable spinner in the h1 element */
        $('.fa-spinner.fa-spin').removeClass('hidden');
    },
    success: function (file, response) {
        var json = jQuery.parseJSON(response);
        if (json) {
            if (json.success === true) {
                if (json.pathologies) {
                    $('#table_pathologies').show();
                    $.each(json.pathologies, function (key, values) {
                        populateStatisticTable(values.id, values.name, values.status);
                    });
                }
            } else {
                writeMsg(json.error, 'danger');
            }
        }
    },
    complete: function (file) {
        $('.fa-spinner.fa-spin').addClass('hidden');
        pathologiesDropzone.removeFile(file);
    }
});

/**
 * Write a message in the msg div.
 * @param {string} msg
 * @param {string} type
 * 
 */
function writeMsg(msg, type = 'success') {
    msg = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' + msg + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
    $('#msg').prepend(msg);
}

/**
 * Populated the statistic's table with the pathologies returned from the server.
 * @param {int} id
 * @param {string} name
 * @param {string} status
 * @returns {undefined}     
 */
function populateStatisticTable(id, name, status) {
    /**
     * Create and store a DataTable istance.
     */
    if (table_pathologies === null) {
        table_pathologies = $('#table_pathologies').DataTable({
            "lengthChange": false,
            "pageLength": 10,
            "order": [[2, "desc"]],
            "ordering": true
        });
        $('.table_pathologies').removeClass('hidden');
    }
    if (!lastInsertedIdStatistic.hasOwnProperty(id)) {
        table_pathologies.row.add([id, name, status]).draw();
        lastInsertedIdStatistic[id] = id;
    }
}
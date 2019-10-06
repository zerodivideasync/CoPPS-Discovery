/**
 * @fileOverview Various tool functions to elaborate Pathologies page under templates/Pathology/.
 * @author <a href="mailto:s@example.com">Simone Mosi</a>
 * @author <a href="mailto:s@example.com">Luca Ioffredo</a>
 * @version 1.0
 */

/**
 * DataTable instance.
 * @type {object}
 */
var table_pathologies = null;
/**
 * Contains some labels.
 * @namespace 
 */
var labels_form = {"insert": "Insert new pathology", "edit": "Edit pathology"};
/**
 * Contains url for form's action.
 * @namespace
 */
var actions_form = {"insert": "insert.php", "edit": "edit.php"};

/**
 * Reset the form's label.
 * @param {object} btn
 */
function resetButton(btn) {
    $('#label-form').text(labels_form["insert"]);
    $('#op').val('insert');
}

/**
 * Prepares a submit to an ajax.
 * It can edit or insert a pathology.
 * The server's response is always a JSON.
 * @returns {boolean}
 */
function submitButton() {
    /**
     * Enables a spinner to show a loading icon.
     */
    $('button[type="submit"]').html('<i class="fa fa-spinner fa-spin text-submit" style="font-size:24px"></i>');
    /**
     * Default case is insert operation in the form.
     */
    switch ($('#op').val()) {
        case 'insert':
            var name = $('#name_pathology').val();
            var id = null;
            $.ajax({
                method: "POST",
                data: {'name': name},
                url: actions_form["insert"]
            }).done(function (result) {
                var json = jQuery.parseJSON(result);
                if (json) {
                    /**
                     * If the json.success is TRUE then we add a new row to the DataTable instance,
                     * then set page to last.
                     */
                    if (json.success === true) {
                        writeMsg(json.msg);
                        id = json.id;
                        /**
                         * Add a new node in the DataTable istance.
                         */
                        var node = table_pathologies.row.add(
                                [id, name, '<button type="button" class="btn btn-warning btn-pathologies-table" onclick="editPathology(' + id + ', \'' + name + '\')">Edit</button> <button type="button" class="btn btn-danger btn-pathologies-table">Delete</button>']
                                ).draw().node();
                        /**
                         * We set the id and add some class. Then, the page is set to the last.
                         */
                        $(node).attr('id', id);
                        $(node).find('td:nth-child(1)').addClass('text-center id-pathologies-table');
                        $(node).find('td:nth-child(2)').addClass('name-pathologies-table');
                        $(node).find('td:nth-child(2)').addClass('options-table');
                        table_pathologies.page('last').draw('page');
                    } else {
                        writeMsg(json.msg, 'danger');
                    }
                } else {
                    console.log(json);
                }
            }).fail(function (result) {
                alert("Error: " + result.status);
            }).always(function () {
                $('button[type="submit"]').html('Submit');
                $('button[type="reset"]').click();
            });
            break;
        case 'edit':
            var name = $('#name_pathology').val();
            var id = $('#id_pathology').val();
            $.ajax({
                method: "POST",
                data: {'id': id, 'name': name},
                url: actions_form["edit"]
            }).done(function (result) {
                var json = jQuery.parseJSON(result);
                if (json) {
                    /**
                     * If the json.success is TRUE then we edit the row selected in the DataTable instance.
                     */
                    if (json.success === true) {
                        writeMsg(json.msg);
                        $('tr#' + id).find('td:nth-child(2)').text(name);
                    } else {
                        writeMsg(json.msg, 'danger');
                    }
                } else {
                    console.log(json);
                }
            }).fail(function (result) {
//                alert("Error: " + result.status);
            }).always(function () {
                $('button[type="submit"]').html('Submit');
                $('button[type="reset"]').click();
                /**
                 * Restores default insert operation in the form.
                 */
                $('#op').val('insert');
            });
            break;
        default:
            alert("Error");
    }
    return false;
}

/**
 * Prepares an ajax request to delete a pathology from the table.
 * The server's response is always a JSON.
 * @param {object} row
 * @returns {boolean}
 */
function deletePathology(row) {
    var id = $(row).attr('id');
    var name = $(row).children('td.name-pathologies-table').text();
    if (!id) {
        alert("Id error");
        return false;
    }
    if (!name) {
        alert("Name error");
        return false;
    }
    if (!confirm("Pathology " + name + " will be deleted. Are you sure?")) {
        return false;
    }
    /**
     * Enables a spinner to show a loading icon.
     */
    $(row).find('td.options-table > button.btn-danger').html('<i class="fa fa-spinner fa-spin hidden" style="font-size:24px"></i>');
    $.ajax({
        method: "POST",
        data: {'id': id, 'name': name},
        url: "./delete.php"
    }).done(function (result) {
        var json = jQuery.parseJSON(result);
        if (json) {
            /**
             * If the json.success is TRUE then we remove the row selected from the DataTable instance.
             */
            if (json.success === true) {
                writeMsg(json.msg);
                table_pathologies
                        .row($(row))
                        .remove()
                        .draw();
            } else {
                writeMsg(json.msg, 'danger');
                $(row).find('td.options-table > button.btn-danger').html('Delete');
            }
        } else {
            console.log(json);
        }
    }).fail(function (result) {
//        alert("Error: " + result.status);
    }).always(function () {
        /**
         * Restores default insert operation in the form.
         */
        $('#op').val('insert');
        $('#name_pathology').val('');
        $('#id_pathology').val('');
        /**
         * Enables a spinner to show a loading icon.
         */
        $(row).find('td.options-table > button.btn-danger').html('Delete');
    });
}

/**
 * Sets some parameters before edit a pathology selected in the table.
 * @param {int} id
 * @param {string} name
 */
function editPathology(id, name) {
    $('#op').val('edit');
    $('#label-form').text(labels_form["edit"]);
    $('#id_pathology').val(id);
    $('#name_pathology').val(name);
}

/**
 * Opens the diagnosis's page to show all diagnoses with this pathology id.
 * @param {int} id
 * @param {string} url 
 */
function viewPathology(id,url) {
    if(!url || !id) return;
    window.open(url+'?idp='+id, '_blank');
}

/**
 * Write a message in the msg div.
 * @param {string} msg
 * @param {string} type
 * 
 */
function writeMsg(msg, type = 'success') {
    msg = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' + msg + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>'
    $('#msg').prepend(msg);
    $('html, body').animate({
        scrollTop: $('#msg').offset().top
    }, 800);
    $('#msg').focus();
}

/**
 * Function that binds the deletePathology function to the trash button showed in each rows of the DataTable.
 */
function bindEvents() {
    $('#table_pathologies tbody').unbind('click');
    $('#table_pathologies tbody').on('click', 'button.btn-danger', function () {
        deletePathology($(this).parents('tr'));
    });
}

$(document).ready(function () {
    /**
     * Create and store a DataTable istance.
     */
    table_pathologies = $('#table_pathologies').DataTable({
        "lengthChange": false,
        "pageLength": 10,
        "ordering": true,
        "columnDefs": [{
                "targets": 2,
                "searchable": false,
                "sortable": false
            }]
    });
    bindEvents();
});
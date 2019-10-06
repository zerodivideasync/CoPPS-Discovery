/**
 * @fileOverview Various tool functions to elaborate diagnoses.
 * @author <a href="mailto:s@example.com">Simone Mosi</a>
 * @author <a href="mailto:s@example.com">Luca Ioffredo</a>
 * @version 1.0
 */

/**
 * Debug variable.
 * If TRUE enable each debug print.
 * @type {boolean}
 */
var debug = false;
/** The object google.maps.Map created by Google Maps API after initMap function 
 * @see {@link initMap} for further information.
 * @type {google.maps.Map}
 * */
var map = null;
var geocoder = null;
/** @namespace */
var counter = {
    /** 
     * Counter's id.
     * @type {int}
     * */
    id: 0,
    /**
     * Returns last generated id.
     * @returns {int}
     */
    getId: function () {
        return this.id;
    },
    /**
     * Increments last generated id than returns it.
     * @returns {int}
     */
    newId: function () {
        return ++this.id;
    }
};
/**
 * It will be TRUE after document is ready.
 * @type {boolean} 
 * */
var first_loaded = false;
/**
 * The DrawingManager class provides a graphical interface for users to draw polygons, rectangles, polylines, circles, and markers on the map.
 * It's part of GoogleMaps API.
 * @type {google.maps.drawing.DrawingManager}
 */
var drawingManager = null;
/**
 * Current Shape created by Google Maps API.
 * @type {Shape}
 * @see {@link Shape} for further information.
 */
var pendingShape = null;
/**
 * Store all Shapes to save in database created by user.
 * @type {Shape[]}
 */
var insertedShapes = {};
/**
 * Store all Shapes marked as "deleted".
 * @type {Shape[]}
 */
var deletedShapes = {};
/**
 * Store all Shapes marked as "edited".
 * @type {Shape[]}
 */
var editedShapes = {};
/**
 * Store all Shapes retrieved by database.
 * Each element is an array contains all Shapes retireved by database. 
 * It is populated at runtime at each request sends by the user.
 * @type {object[]}
 */
var tooltipInsertedShapes = {};
/**
 * It rapresents the last event selected by the user.
 * @type {google.maps.drawing.OverlayType.MARKER|CIRCLEPOLYGONPOLYLINE}
 */
var lastDrawer = null;
/**
 * Contains the pathologies retrieved from server.
 * @type {object[]}
 */
var pathologiesList = [];
/**
 * Contains the pathology's id to show only a subset of diagnosis with this pathology.
 * @type {boolean|int}
 */
var id_pathology = false;
/**
 * Shape class
 * 
 * @class
 */
class Shape {
    /**
     * Creates an instance of Shape.
     * 
     * @constructor
     * 
     * @param {int} id 
     * Id of this Shape.
     * @param {mixed} type 
     * It rapresents the type of this shape. The value may be Boolean (default FALSE) or string.
     * @param {google.maps.Map} overlay
     * The object google.maps.Map created by Google Maps API after an event "overlaycomplete". 
     * It may be NULL if not defined or Object.
     * 
     * @this {Shape}
     */
    constructor(id = 0, type = false, overlay = null) {
        /**
         * The id of the shape.
         * @type {int}
         */
        this.id = id;
        /**
         * The id of the pathology.
         * @type {int}
         */
        this.idPathology = 0;
        /**
         * The start-date of the shape.
         * @type {string}
         */
        this.date = '';
        /**
         * The type of the shape.
         * @type {mixed}
         */
        this.type = type;
        /**
         * The object that contains all points of the shape.
         * @type {object}
         */
        this.values = {};
        /**
         * The object that contains all points of the shape.
         * It may be google.maps.Map or NULL.
         * @type {mixed} 
         */
        this.overlay = overlay;
    }

    /**
     * Edit some params of this Shape.
     * @param {int} id of the pathology 
     * Pathology of this Shape.
     * @param {string} date 
     * Date in a string format as dd/mm/YYYY or similar.
     * 
     * @this {Shape}
     */
    edit(idPathology, date) {
        this.idPathology = idPathology;
        this.date = date;
        this.type = 'edit';
    }

    /**
     * Set the state of this Shape as type to "delete".
     * @param {int} id 
     * Id of this Shape.
     * 
     * @this {Shape}
     */
    remove(id) {
        this.id = id;
        this.type = 'delete';
    }

    /**
     * Save the state of this Shape.
     * @param {int} idPathology 
     * @param {string} date 
     * Date in a string format as dd/mm/YYYY or similar.
     * @param {object} icons 
     * @see {@link icons} for further information.
     * An object with attributes complete, pending, edited, delete. It's user defined.
     * @this {Shape}
     * @return {object} Machine-readable representation of this Shape on success, FALSE if the request fails.
     */
    save(idPathology, date, icons) {
        if (!this.overlay) {
            return false;
        }
        this.idPathology = idPathology;
        this.date = date;
        this.idPathology = idPathology;
        switch (this.type) {
            case google.maps.drawing.OverlayType.MARKER:
                this.overlay.setIcon({
                    url: icons.complete.icon,
                    scaledSize: new google.maps.Size(30, 30)
                });
                break;
            case google.maps.drawing.OverlayType.CIRCLE:
            case google.maps.drawing.OverlayType.POLYGON:
            case google.maps.drawing.OverlayType.POLYLINE:
                this.overlay.setOptions({
                    strokeColor: '#13C800',
                    strokeOpacity: 0.8,
                    strokeWeight: 4,
                    fillColor: '#13C800',
                    fillOpacity: 0.35
                });
                break;
            default:
                return false;
        }
        return this;
    }

    /**
     * Find a Object representation of this Shape.
     * 
     * @this {Shape}
     * @return {Object} Machine-readable representation of this Shape on success, FALSE if the request fails.
     */
    toObj() {
        switch (this.type) {
            case google.maps.drawing.OverlayType.MARKER:
                this.values = {
                    lat: this.overlay.getPosition().lat(),
                    lng: this.overlay.getPosition().lng()
                };
                break;
            case google.maps.drawing.OverlayType.CIRCLE:
                this.values = {
                    lat: this.overlay.getCenter().lat(),
                    lng: this.overlay.getCenter().lng(),
                    radius: this.overlay.getRadius()
                };
                break;
            case google.maps.drawing.OverlayType.POLYGON:
            case google.maps.drawing.OverlayType.POLYLINE:
                this.values = {
                    points: []
                }
                var vertices = this.overlay.getPath();
                for (var i = 0; i < vertices.getLength(); i++) {
                    var xy = vertices.getAt(i);
                    this.values.points[i] = {
                        lat: xy.lat(),
                        lng: xy.lng()
                    }
                }
                break;
            case 'edit':
                return {
                    id: this.id,
                    idPathology: this.idPathology,
                    date: this.date,
                }
                break;
            case 'delete':
                return {
                    id: this.id
                }
                break;
            default:
                return false;
        }
        return {
            id: this.id,
            idPathology: this.idPathology,
            date: this.date,
            type: this.type,
            values: this.values
        }
    }
}

/**
 * This will be launch as first function.
 * 
 */
$(document).ready(function () {
    /**
     * This binds a function on change status of Search checkbox.
     * It checks the checked state before {@link searchArea} function.
     * 
     */
    $('#search_button').change(function (event) {
        var state = $(this).is(':checked');
        if (debug)
            console.log("Search button toggle: " + state);
        if (state) {
            searchArea(map.getBounds().getNorthEast(), map.getBounds().getSouthWest());
            $(this).parent().removeClass('btn-outline-info');
            $(this).parent().addClass('btn-info');
            $('#text_search').text('Search enabled');
        } else {
            $(this).parent().removeClass('btn-info');
            $(this).parent().addClass('btn-outline-info');
            $('#text_search').text('Search disabled');
        }
    });
});

/**
 * Preprocess each Shape inserted, edited or deleted by the user before submit.
 * @returns {Boolean}
 */
function prepBeforeSubmit() {
    /**
     * Iterates on each element of {@link insertedShapes} to preprocess data before parsing as JSON.
     * @see {@link toObj} for further information. 
     */
    $.each(insertedShapes, function (key, elem) {
        insertedShapes[key] = insertedShapes[key].toObj();
    });
    /**
     * Iterates on each element of {@link deletedShapes} to preprocess data before parsing as JSON.
     * @see {@link toObj} for further information. 
     */
    $.each(deletedShapes, function (key, elem) {
        deletedShapes[key] = deletedShapes[key].toObj();
    });
    /**
     * Iterates on each element of {@link editedShapes} to preprocess data before parsing as JSON.
     * @see {@link toObj} for further information. 
     */
    $.each(editedShapes, function (key, elem) {
        editedShapes[key] = editedShapes[key].toObj();
    });
    if (debug)
        console.log(JSON.stringify(insertedShapes));
    /**
     * Parse each array as JSON string and store them into three input elements.
     */
    $('#shapes_to_insert').val(JSON.stringify(insertedShapes));
    $('#shapes_to_delete').val(JSON.stringify(deletedShapes));
    $('#shapes_to_edit').val(JSON.stringify(editedShapes));
    return true;
}

/**
 * Binds datapickers to the infowindow after the event domready.
 * It is used both from new drawn Shape and retrieved data from database. 
 * @param {google.maps.InfoWindow} infowindow
 * @param {int} id
 * @param {boolean} type
 * 
 */
function bindDatepicker(infowindow, id, type = false) {
    var id_type = "";
    /**
     * If is not FALSE than the infowindows rapresents a Shape retrieved from database.
     */
    if (type) {
        id_type = "_retrieved";
    }
    google.maps.event.addListener(infowindow, 'domready', function () {
        $("#date" + id_type + "_id_" + id + ", #date_to_shape" + id_type + "_id_" + id).datepicker({
            dateFormat: "dd/mm/yy",
            changeYear: true,
            changeMonth: true,
            constrainInput: true,
            gotoCurrent: true,
            maxDate: "+0d",
            /**
             * After the user changes the date by datepicker this enforces a minimum and a maximum range.
             * @param {string} dateText
             * @param {object} dateObject
             */
            onSelect: function (dateText, dateObject) {
                if (dateObject.id === "date" + id_type + "_id_" + id) {
                    $("#date_to_shape" + id_type + "_id_" + id).datepicker("option", "minDate", $.datepicker.parseDate(dateObject.settings.dateFormat, dateText));
                } else if (dateObject.id === "date_to_shape" + id_type + "_id_" + id) {
                    $("#date" + id_type + "_id_" + id).datepicker("option", "maxDate", $.datepicker.parseDate(dateObject.settings.dateFormat, dateText));
                }
            }
        });
        var dateFrom = $("#date" + id_type + "_id_" + id).val();
        var dateTo = $("#date_to_shape" + id_type + "_id_" + id).val();
        /**
         * After the user changes the date by datepicker this enforces a minimum and a maximum range.
         * This work like onSelect function.
         */
        if (dateFrom) {
            $("#date_to_shape" + id_type + "_id_" + id).datepicker("option", "minDate", $.datepicker.parseDate("dd/mm/yy", dateFrom));
        }
        if (dateTo) {
            $("#date" + id_type + "_id_" + id).datepicker("option", "maxDate", $.datepicker.parseDate("dd/mm/yy", dateTo));
        }
    });
}

/**
 * Binds a tooltip to the id.
 * @param {int} id
 * 
 */
function bindTooltip(id) {
    $('#tooltip_shape_id_' + id + '[data-toggle="tooltip"]').tooltip();
}

/**
 * Binds a Select2 combobox to the infowindow after the event domready.
 * It is used both from new drawn Shape and retrieved data from database.
 * @param {google.maps.InfoWindow} infowindow
 * @param {int} id
 * @param {boolean} type
 * @param {int} idPathology
 * 
 */
function bindSelect2Combobox(infowindow, id, type = false, idPathology = false) {
    var id_type = "";
    /**
     * If is not FALSE than the infowindows rapresents a Shape retrieved from database.
     */
    if (type) {
        id_type = "_retrieved";
    }
    google.maps.event.addListener(infowindow, 'domready', function () {
        $("#name_pathology" + id_type + "_id_" + id).select2({
            placeholder: "Select a pathology",
            allowClear: false,
            dropdownAutoWidth: true,
            theme: 'classic',
            width: '100%'
        });
        if (type && idPathology) {
            setSelectedPathology(id, idPathology);
        }
    });
}

/**
 * Enforces that only one Tooltip is open a time.
 * The Tooltip is rapresented by InfoWindow of Google Maps API.
 * @param {int} id
 * @param {boolean} retrieved
 * 
 */
function onlyOneTooltip(id = false, retrieved = false) {
    /**
     * If there is a drawn shape still active on the map and the user click on a retrieved shape,
     * than this close each tooltip except the last.
     */
    if (pendingShape && id && retrieved) {
        pendingShape.overlay.tooltip.close();
        pendingShape.overlay.setMap(null);
        enableUI(map);
        pendingShape = null;
    }
    $.each(tooltipInsertedShapes, function (index, value) {
        if (index != id) {
            value['tooltip'].close();
        }
    });
    $.each(insertedShapes, function (index, value) {
        if (index != id) {
            value.overlay.tooltip.close();
        }
    });
}

function createListOption(options = []) {
    var res = "";
    if (!options) {
        return res;
    }
    for (var i = 0; i < options.length; i++) {
        res += "<option value=\"" + options[i].id + "\">" + options[i].name + "</option>";
    }
    return res;
}

/**
 * Returns a HTML code string with the content of ToolTip of a overlay created by Google Maps API.
 * @param {int} id
 * @param {int} idPathology
 * @param {string} date
 * @returns {String}
 */
function printTooltipForm(id, idPathology = false, date = "") {
    var readonly = "";
    var extra_button = '<button id="submit_shape_id_' + id + '" onclick="saveInfo(\'insert\');" class="btn btn-outline-success btn-pollution ml-4"><i class="fas fa-check-square"></i></button>';
    var id_type = "";
    var tooltip = '<i id="tooltip_shape_id_' + id + '" data-html="true" data-toggle="tooltip" title="After submit the <strong><u>position</u></strong> will be permanent while <strong><u>name</u></strong> and <strong><u>dates</u></strong> can be changed." class="fas fa-info-circle"></i>';
    /**
     * If is not empty than the id rapresents a Shape retrieved from database.
     */
    if (idPathology) {
        id_type = "_retrieved";
        extra_button = '<button id="edit_shape_id_' + id + '" onclick="saveInfo(\'edit\', ' + id + ');" class="btn btn-outline-warning btn-pollution ml-2"><i class="fas fa-pencil-alt"></i></button>';
        extra_button += '<button id="view_shape_id_' + id + '" onclick="viewPathology(' + idPathology + ');" class="btn btn-outline-info btn-pollution ml-2"><i class="fas fa-eye"></i></button>';
        tooltip = "";
    }
    return '<div id="content_shape' + id_type + '_id_' + id + '" class="container container-infowindow">' +
            '<h6>Insert new diagnosis ' + tooltip + '</h6>' +
            '<div class="row-fluid text-center">' +
            '</div>' +
            '<form action="#" class="form form-pollution p-1" method="POST" onsubmit="return false;" autocomplete="off">' +
            '<div class="row mb-2">' +
            '<label class="col-xs-12 col-sm-12 col-md-12 col-lg-12 label-pollution" for="name_pathology' + id_type + '_id_' + id + '">Pathology:</label>' +
            '<select style="width: 92%;" class="form-control col js-basic-single js-responsive" id="name_pathology' + id_type + '_id_' + id + '" name="name_pathology' + id_type + '_id_' + id + '" required ' + readonly + '>' +
            '<option value="">-</option>' +
            createListOption(pathologiesList) +
            '</select> ' +
            '</div>' +
            '<div class="row">' +
            '<label class="col-xs-4 col-sm-4 col-md-2 col-lg-2 label-pollution" for="date' + id_type + '_id_' + id + '">Data:</label>' +
            '<input class="form-control col-xs-8 col-sm-8 col-md-4 col-lg-4 datepicker" placeholder="dd/mm/yyyy" id="date' + id_type + '_id_' + id + '" name="date' + id_type + '_id_' + id + '" value="' + date + '" required type="text" ' + readonly + '>' +
            '</div>' +
            '<div class="row mt-2">' +
            '<label class="col-xs-12 col-sm-12 col-md-3 col-lg-3 label-pollution" for="address' + id_type + '_id_' + id + '">Address:</label>' +
            '<span class="col-xs-8 col-sm-8 col-md-9 col-lg-9 pt-1 m-0" id="address_id_' + id + '">Not retrieved</span>' +
            '</div>' +
            '<div class="row">' +
            '<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 text-center mt-2">' +
            '<p class="font-red">Remember to click on the <i>Save all changes</i> button!</p>' +
            '</div>' +
            '<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 text-right mt-2">' +
            '<button class="btn btn-outline-danger btn-pollution" onclick="saveInfo(\'delete\', ' + id + ');"><i class="fas fa-trash-alt"></i></button>' +
            extra_button +
            '</div>' +
            '</div>' +
            '</form>' +
            '</div>';
}

/**
 * Opens the diagnosis's page to show all diagnoses with this pathology id.
 * @param {int} id
 */
function viewPathology(id) {
    if (!id)
        return;
    var url = window.location.href.replace(/[\?#].*|$/, '?idp=' + id);
    window.open(url, '_self');
}

function setSelectedPathology(id, idPathology) {
    if (!id || !idPathology || isNaN(idPathology)) {
        return false;
    }
    $('select#name_pathology_retrieved_id_' + id).val(idPathology).trigger('change.select2');
}

/**
 * SaveInfo make some operation about a Shape.
 * @param {string} op 
 * The value is one of: insert, edit, delete.
 * With insert SaveInfo prepares the {@link pendingShape} in {@link insertedShapes}.
 * With edit SaveInfo prepares the Shape selected in {@link editedShapes}.
 * With delete SaveInfo prepares the Shape selected in {@link deletedShapes}.
 * @param {int|boolean} id
 * @returns {Boolean}
 */
function saveInfo(op, id = false) {
    var idPathology = false;
    var date = false;
    var shape = false;
    switch (op) {
        /**
         * This case is only for inserting new shape. It is not for retrieved data.
         */
        case 'insert':
            if (pendingShape !== null) {
                date = $('#date_id_' + pendingShape.id).val();
                idPathology = $('#name_pathology_id_' + pendingShape.id).val();
                if (!date || !idPathology) {
                    return false;
                }
                /**
                 * @see {@link Shape#save}
                 * @type {boolean|object|Shape}
                 */
                shape = pendingShape.save(idPathology, date, icons);
                if (shape) {
                    insertedShapes[shape.id] = shape;
                    disableFormTooltip();
                    shape.overlay.tooltip.close();
                    enableUI(map);
                    pendingShape = null;
                } else {
                    alert("Error saving shape! Id: " + pendingShape.id);
                }
            } else {
                alert("Error saving shape!");
            }
            break;
            /**
             * This case is only for retrieved shape. It is not for new data.
             */
        case 'edit':
            /** Enforces that id is a valid number. */
            if (Number.isInteger(id) && Number.parseInt(id) > 0) {
                idPathology = $('#name_pathology_retrieved_id_' + id).val();
                date = $('#date_retrieved_id_' + id).val();
                /**
                 * @see {@link Shape}
                 * @type {Shape}
                 */
                shape = new Shape(id, false, null);
                shape.edit(idPathology, date);
                editedShapes[id] = shape;
                /**
                 * prot1 and prot2 are required to analize tooltipInsertedShapes's property.
                 * @type {object}
                 */
                var prot1 = null, prot2 = null;
                /** 
                 * First level of __proto__
                 */
                prot1 = Object.getPrototypeOf(tooltipInsertedShapes[id]['shape']);
                /** 
                 * Second level of __proto__
                 */
                prot2 = Object.getPrototypeOf(prot1);
                /**
                 * If prot2 has setIcon property/function than tooltipInsertedShapes[id]['shape'] is a Marker.
                 */
                if (prot2.hasOwnProperty('setIcon')) {
                    /**
                     * With setIcon it changes the icon's state.
                     */
                    tooltipInsertedShapes[id]['shape'].setIcon({
                        url: icons.edited.icon,
                        scaledSize: new google.maps.Size(30, 30)
                    });
                } else {
                    /**
                     * If prot2 hasn't setIcon property/function than tooltipInsertedShapes[id]['shape'] is a Polygon.
                     * With setOptions function it changes color's polygon
                     */
                    tooltipInsertedShapes[id]['shape'].setOptions({
                        strokeColor: '#13C800',
                        strokeOpacity: 0.8,
                        strokeWeight: 4,
                        fillColor: '#13C800',
                        fillOpacity: 0.35
                    });
                }
                /**
                 * Enforce the closure of tooltip.
                 */
                tooltipInsertedShapes[id]['tooltip'].close();
            }
            break;
            /**
             * This case is for both new drawn shapes and retrieved data.
             */
        case 'delete':
            if (Number.isInteger(id) && Number.parseInt(id) >= 0) {
                /**
                 * Check if id rapresents a drawn shape.
                 */
                if (pendingShape !== null || insertedShapes.hasOwnProperty(id)) {
                    deleteShape(id);
                } else { /** Else id rapresents a retrieved shape. */
                    /**
                     * @see {@link Shape}
                     * @type {newShape}
                     */
                    shape = new Shape(id, false, null);
                    shape.remove(id);
                    /**
                     * prot1 and prot2 are required to analize tooltipInsertedShapes's property.
                     * @type {object}
                     */
                    var prot1 = null, prot2 = null;
                    /** 
                     * First level of __proto__
                     */
                    prot1 = Object.getPrototypeOf(tooltipInsertedShapes[id]['shape']);
                    /** 
                     * Second level of __proto__
                     */
                    prot2 = Object.getPrototypeOf(prot1);
                    /** Store the deleted shape */
                    deletedShapes[id] = shape;
                    /**
                     * If prot2 has setIcon property/function than tooltipInsertedShapes[id]['shape'] is a Marker.
                     */
                    if (prot2.hasOwnProperty('setIcon')) {
                        tooltipInsertedShapes[id]['shape'].setIcon({
                            url: icons.deleted.icon,
                            scaledSize: new google.maps.Size(30, 30)
                        });
                    } else {
                        /**
                         * If prot2 hasn't setIcon property/function than tooltipInsertedShapes[id]['shape'] is a Polygon.
                         * With setOptions function it changes color's polygon
                         */
                        tooltipInsertedShapes[id]['shape'].setOptions({
                            strokeColor: '#13C800',
                            strokeOpacity: 0.8,
                            strokeWeight: 4,
                            fillColor: '#13C800',
                            fillOpacity: 0.35
                        });
                    }
                    /**
                     * Enforce the closure of tooltip.
                     */
                    tooltipInsertedShapes[id]['tooltip'].close();
                    delete tooltipInsertedShapes[id];
                }
            }
            break;
    }
    return false;
}

/**
 * deleteShape delete the current drawn Shape from the Map.
 * @param {int} id
 * @returns {Boolean}
 */
function deleteShape(id) {
    /** 
     * @type {Shape}
     */
    var shape = null;
    if (pendingShape !== null) {
        shape = pendingShape;
    } else if (id && insertedShapes.hasOwnProperty(id)) {
        shape = insertedShapes[id];
    }
    if (shape) {
        /** Close the tooltip of this deleted Shape. */
        shape.overlay.tooltip.close();
        /** Set a null Map for this deleted Shape, so this shape vanish. */
        shape.overlay.setMap(null);
        /** Delete the element from {@link insertedShapes} */
        if (insertedShapes.hasOwnProperty(shape.id)) {
            delete insertedShapes[shape.id];
        }
        shape = null;
        pendingShape = null;
        /** Enable each interaction of the user */
        enableUI(map);
        return true;
    }
    return false;
}

/**
 * Disable the form in the Tooltip of the current drawn Shape on the Map.
 * 
 */
function disableFormTooltip() {
    if (pendingShape !== null) {
        $('#name_pathology_id_' + pendingShape.id).prop('readonly', true);
        $('#date_id_' + pendingShape.id).prop('readonly', true);
        $('#date_to_shape_id_' + pendingShape.id).prop('readonly', true);
        $('#submit_shape_id_' + pendingShape.id).prop('disabled', true);
        $("#date_id_" + pendingShape.id).datepicker("option", "disabled", true);
    }
}

/**
 * Disable any interaction of the user.
 * @param {google.maps.Map} map
 * 
 */
function disableUI(map) {
    /** Disable draggable, zoom, scrool and doubleclick control */
    map.setOptions({
        draggable: false,
        zoomControl: false,
        scrollwheel: false,
        disableDoubleClickZoom: true,
        disableDefaultUI: true});
    /** Without arguments setDrawingMode() set the current cursor to hand */
    drawingManager.setDrawingMode();
    /** Disable drawing control on the map */
    drawingManager.setOptions({
        drawingControl: false
    });
}

/**
 * Enable each interaction of the user.
 * @param {google.maps.Map} map
 * 
 */
function enableUI(map) {
    /** Enable draggable, zoom, scrool and doubleclick control */
    map.setOptions({
        draggable: true,
        zoomControl: true,
        scrollwheel: true,
        disableDoubleClickZoom: false,
        disableDefaultUI: false});
    /** Enable drawing control on the map */
    drawingManager.setOptions({
        drawingControl: true
    });
    /** With {@link lastDrawer} as argument setDrawingMode() set the current cursor to last mode selected by the user */
    drawingManager.setDrawingMode(lastDrawer);
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
}

/**
 * Sends the current boundary of the map to the server to retrieve new data.
 * It uses ajax method of jQuery.
 * @param {google.maps.LatLng} ne
 * @param {google.maps.LatLng} sw
 * @returns {Boolean|undefined}
 */
function searchArea(ne, sw) {
    if (!ne || !sw)
        return false;
    /**
     * Store all shape's id before launch ajax request.
     * @type {int[]}
     */
    var loaded = [];
    $.each(tooltipInsertedShapes, function (key, value) {
        /** 
         * Iterate on each key that rapresent the id of a Shape.
         * */
        loaded.push(key);
    });
    $.each(deletedShapes, function (key, value) {
        /** 
         * Iterate on each key that rapresent the id of a Shape.
         * */
        loaded.push(key);
    });
    /** Enable spinner in the h1 element */
    $('.fa-spinner.fa-spin').removeClass('hidden');
    $.ajax({
        method: "POST",
        data: {
            'ne_lat': ne.lat(),
            'ne_lng': ne.lng(),
            'sw_lat': sw.lat(),
            'sw_lng': sw.lng(),
            'id_pathology': id_pathology,
            /**
             * Parse loaded as a JSON string.
             */
            'loaded': JSON.stringify(loaded)
        },
        url: 'ajaxArea.php'
    }).done(function (result) {
        var json = jQuery.parseJSON(result);
        if (json) {
            if (json.success === true) {
                /** Diagnoses */
                if (json.data_found.diagnoses) {
                    $.each(json.data_found.diagnoses, function (key, values) {
                        var id = values.id;
                        tooltipInsertedShapes[id] = {};
                        tooltipInsertedShapes[id]['type'] = 'diagnosis';
                        tooltipInsertedShapes[id]['shape'] = new google.maps.Marker({
                            position: {
                                lat: parseFloat(values.shape.coordinates[0][1]),
                                lng: parseFloat(values.shape.coordinates[0][0])
                            },
                            map: map,
                            draggable: false,
                            editable: false,
                            icon: {
                                url: icons.diagnosis.icon,
                                scaledSize: new google.maps.Size(30, 30), // scaled size
                            },
                            title: values.name
                        });
                        tooltipInsertedShapes[id]['tooltip'] = new google.maps.InfoWindow({
                            content: printTooltipForm(id, values.id_pathology, values.date)
                        });
                        tooltipInsertedShapes[id]['shape'].addListener('click', function () {
                            if (!tooltipInsertedShapes.hasOwnProperty(id))
                                return false;
                            tooltipInsertedShapes[id]['tooltip'].open(map, tooltipInsertedShapes[id]['shape']);
                            onlyOneTooltip(id, true);
                            /** Get address using coordinates with google API */
                            var newShape = {
                                id_custom: id,
                                position: {
                                    lat: function () {
                                        return values.shape.coordinates[0][1];
                                    },
                                    lng: function () {
                                        return values.shape.coordinates[0][0];
                                    }
                                }
                            };
                            /* To avoid overload OVER_QUERY_LIMIT on google WS, we load address only on click */
                            geocodeLatLng(geocoder, map, newShape);
                        });
                        bindDatepicker(tooltipInsertedShapes[id]['tooltip'], id, true);
                        bindSelect2Combobox(tooltipInsertedShapes[id]['tooltip'], id, true, values.id_pathology);
                    });
                }
            } else {
                writeMsg(json.msg, 'danger');
            }
        } else {
            console.log(json);
        }
    }).fail(function (result) {
//            alert("Error: " + result.status);
    }).always(function () {
        /** Disable spinner in the h1 element */
        $('.fa-spinner.fa-spin').addClass('hidden');
    });
}

function geocodeLatLng(geocoder, map, newShape) {
    var id = newShape.id_custom;
    var latlng = {lat: parseFloat(newShape.position.lat()), lng: parseFloat(newShape.position.lng())};
    geocoder.geocode({'location': latlng}, function (results, status) {
        if (status === 'OK') {
            if (results[0]) {
                $('#address_id_' + id).html(results[0].formatted_address);
            } else {
                $('#address_id_' + id).html('No address found');
            }
        } else {
            $('#address_id_' + id).html(status);
        }
    });
}


/** 
 * Core of all logic.
 * Initialize the Map with drawing manager and all listener.
 *      
 * */
function initMap() {
    /**
     * Initial position of the map.
     * @type {google.maps.LatLng}
     */
    var myLatlng = null;
    var last_zoom = 9;
    var last_position = getCookie('map_position');
    if (last_position) {
        last_position = JSON.parse(last_position);
        myLatlng = new google.maps.LatLng(parseFloat(last_position.lat), parseFloat(last_position.lng));
        last_zoom = parseInt(last_position.zoom);
    } else {
        myLatlng = new google.maps.LatLng(40.8538487, 14.1065184);
    }
    geocoder = new google.maps.Geocoder;
    /**        
     * Global object that rappresents the Map. 
     * @type {google.maps.Map}
     */
    map = new google.maps.Map(document.getElementById('map'), {
        center: myLatlng,
        zoom: last_zoom,
        gestureHandling: 'greedy'
    });
    /**
     * The control menu with all drawing mode.
     * @type {google.maps.drawing.DrawingManager}
     */
    drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.MARKER,
        drawingControl: true,
        drawingControlOptions: {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: ['marker']
                    /*
                     * Only markers are allowed.
                     * drawingModes: ['marker', 'circle', 'polygon', 'polyline']
                     */
        },
        /**
         * Initial marker's settings.
         * Each initial marker is draggable.
         */
        markerOptions: {
            icon: {
                url: icons.pending.icon,
                scaledSize: new google.maps.Size(30, 30), // scaled size
            },
            draggable: true,
            suppressUndo: true
        },
        /**
         * Initial circle's settings.
         * Each initial circle is draggable, editable and clickable.
         */
        circleOptions: {
            editable: true,
            draggable: true,
            clickable: true,
            suppressUndo: true,
            strokeColor: '#FF6C00',
            strokeOpacity: 0.8,
            strokeWeight: 4,
            fillColor: '#FF6C00',
            fillOpacity: 0.55,
            geodesic: false
        },
        /**
         * Initial polygon's settings.
         * Each initial polygon is draggable, editable and clickable.
         */
        polygonOptions: {
            editable: true,
            draggable: true,
            clickable: true,
            suppressUndo: true,
            strokeColor: '#FF6C00',
            strokeOpacity: 0.8,
            strokeWeight: 4,
            fillColor: '#FF6C00',
            fillOpacity: 0.55,
            geodesic: false
        },
        /**
         * Initial polyline's settings.
         * Each initial polyline is draggable, editable and clickable.
         */
        polylineOptions: {
            editable: true,
            draggable: false,
            clickable: true,
            suppressUndo: true,
            strokeColor: '#FF6C00',
            strokeOpacity: 0.8,
            strokeWeight: 4,
            fillColor: '#FF6C00',
            fillOpacity: 0.55,
            geodesic: false
        }
    });

    /** Without arguments setDrawingMode() set the current cursor to hand */
    drawingManager.setDrawingMode();
    /** Set drawingManager on the map */
    drawingManager.setMap(map);

    /**
     * Attach a listener to drawingManager when the overlaycomplete event is fired.
     * overlaycomplete event is fired when the user drawn a shape on the map.
     */
    google.maps.event.addListener(drawingManager, 'overlaycomplete', function (event) {
        /** 
         * Rapresents the ToolTip of a Shape.
         * @type {google.maps.InfoWindow}
         */
        var tooltip = null;
        /**
         * The object created by overlaycomplete event of Google Maps API.
         * @type {object}
         */
        var newShape = null;
        switch (event.type) {
            case google.maps.drawing.OverlayType.MARKER:
                if (debug)
                    console.log("Insert Marker:");
                newShape = event.overlay;
                shape = newShape;
                /** Generates a new id */
                newShape.id_custom = counter.newId();
                /** Get new form for ToolTip of this shape */
                var html = printTooltipForm(newShape.id_custom);
                tooltip = new google.maps.InfoWindow({
                    content: html
                });
                /** Store the tooltip in newShape */
                newShape.tooltip = tooltip;
                /** 
                 * Attach a listener to newShape when the click event is fired. 
                 * When the user will click on the newShape than the tooltip will be opened.
                 * */
                google.maps.event.addListener(newShape, 'click', function () {
                    tooltip.open(map, newShape);
                    onlyOneTooltip(newShape.id_custom);
                });
                /** 
                 * Attach a listener to newShape when the closeclick event is fired. 
                 * When the user will click on the cross in the tooltip of the shape than the latter will be removed.
                 * */
                google.maps.event.addListener(tooltip, 'closeclick', function () {
                    deleteShape();
                });
                /** 
                 * Attach a listener to newShape when the dragend event is fired. 
                 * When the user will move the shape than the map will be centered.
                 * */
                google.maps.event.addListener(newShape, 'dragend', function () {
                    map.setCenter(this.getPosition());
                    /** Get address using coordinates with google API */
                    geocodeLatLng(geocoder, map, newShape);
                });
                if (debug)
                    console.log(newShape.getPosition().lat() + " " + newShape.getPosition().lng());
                /** Store the new Shape as a {@link pendingShape}*/
                pendingShape = new Shape(newShape.id_custom, event.type, newShape);
                /** Open the current tooltip */
                tooltip.open(map, newShape);
                break;
            case google.maps.drawing.OverlayType.CIRCLE:
                if (debug)
                    console.log("Insert Circle:");
                newShape = event.overlay;
                shape = newShape;
                /** Generates a new id */
                newShape.id_custom = counter.newId();
                /** Get new form for ToolTip of this shape */
                var html = printTooltipForm(newShape.id_custom);
                tooltip = new google.maps.InfoWindow({
                    content: html
                });
                /** Store the tooltip in newShape */
                newShape.tooltip = tooltip;
                /** 
                 * Attach a listener to newShape when the click event is fired. 
                 * When the user will click on the newShape than the tooltip will be opened.
                 * */
                google.maps.event.addListener(newShape, 'click', function () {
                    tooltip.setPosition(newShape.getCenter());
                    tooltip.open(map, newShape);
                    onlyOneTooltip(newShape.id_custom);
                });
                /** 
                 * Attach a listener to newShape when the dragend event is fired. 
                 * When the user will move the shape than the map will be centered.
                 * */
                google.maps.event.addListener(newShape, 'dragend', function () {
                    if (debug)
                        console.log("dragend");
                    tooltip.setPosition(newShape.getCenter());
                    tooltip.open(map, newShape);
                    onlyOneTooltip(newShape.id_custom);
                    /** Get address using coordinates with google API */
                    geocodeLatLng(geocoder, map, newShape);
                });
                /** 
                 * Attach a listener to newShape when the center_changed event is fired. 
                 * When the user will move the shape than the tooltip will be moved on the shape.
                 * */
                google.maps.event.addListener(newShape, 'center_changed', function () {
                    if (debug)
                        console.log("center_changed");
                    tooltip.setPosition(newShape.getCenter());
                    tooltip.open(map, newShape);
                    onlyOneTooltip(newShape.id_custom);
                    /** Get address using coordinates with google API */
                    geocodeLatLng(geocoder, map, newShape);
                });
                /** 
                 * Attach a listener to newShape when the closeclick event is fired. 
                 * When the user will click on the cross in the tooltip of the shape than the latter will be removed.
                 * */
                google.maps.event.addListener(tooltip, 'closeclick', function () {
                    deleteShape();
                });
                /** Store the new Shape as a {@link pendingShape}*/
                pendingShape = new Shape(newShape.id_custom, event.type, newShape);

                if (debug) {
                    console.log("1) Radius: " + newShape.getRadius());
                    console.log("2) Lat: " + newShape.getCenter().lat());
                    console.log("3) Lng: " + newShape.getCenter().lng());
                }
                /** Open the current tooltip */
                tooltip.setPosition(newShape.getCenter());
                tooltip.open(map, newShape);
                break;
            case google.maps.drawing.OverlayType.POLYGON:
            case google.maps.drawing.OverlayType.POLYLINE:
                if (debug)
                    console.log("Insert " + event.type + ":");
                newShape = event.overlay;
                shape = newShape;
                /** Generates a new id */
                newShape.id_custom = counter.newId();
                /** Get new form for ToolTip of this shape */
                var html = printTooltipForm(newShape.id_custom);
                tooltip = new google.maps.InfoWindow({
                    content: html
                });
                /** Store the tooltip in newShape */
                newShape.tooltip = tooltip;
                /** 
                 * Attach a listener to newShape when the click event is fired. 
                 * When the user will click on the newShape than the tooltip will be opened.
                 * */
                google.maps.event.addListener(newShape, 'click', function () {
                    tooltip.setPosition(event.overlay.getPath().getAt(0));
                    tooltip.open(map);
                    onlyOneTooltip(newShape.id_custom);
                });
                /** 
                 * Attach a listener to newShape when the closeclick event is fired. 
                 * When the user will click on the cross in the tooltip of the shape than the latter will be removed.
                 * */
                google.maps.event.addListener(tooltip, 'closeclick', function () {
                    deleteShape();
                });
                /** 
                 * Attach a listener to newShape when the closeclick event is fired. 
                 * When the user will click on the new Shape than the tooltip will be opened.
                 * */
                google.maps.event.addListener(newShape, 'mouseup', function () {
                    if (debug)
                        console.log("mouseup");
                    tooltip.setPosition(newShape.getPath().getAt(0));
                    tooltip.open(map);
                    onlyOneTooltip(newShape.id_custom);
                });
                /** Store the new Shape as a {@link pendingShape}*/
                pendingShape = new Shape(newShape.id_custom, event.type, newShape);
                /** Open the current tooltip */
                tooltip.setPosition(event.overlay.getPath().getAt(0));
                tooltip.open(map);
                break;
            default:
                console.log("Insert default:");
                console.log(event);
        }
        if (newShape !== null) {
            /** Enforces that one tooltip is opened */
            onlyOneTooltip(newShape.id_custom);
            /** Binds each datepicker in the new tooltip */
            bindDatepicker(tooltip, newShape.id_custom);
            /** Binds the tooltip in the form of the shape */
            bindTooltip(newShape.id_custom);
            /** Binds the select2 combobox */
            bindSelect2Combobox(tooltip, newShape.id_custom);
            /** Store last drawning mode used by the user */
            lastDrawer = event.type;
            /** Disable any interaction of the user */
            disableUI(map);
            /** Get address using coordinates with google API */
            geocodeLatLng(geocoder, map, newShape);
        }
    });
    /** 
     * Attach a listener to the map when the idle event is fired. 
     * When the map in idle state, after a interaction with the user, then requests new data.
     * */
    map.addListener('idle', function () {
        if (debug) {
            console.log('Map idle bounds:');
            console.log('Nord Est: ' + map.getBounds().getNorthEast());
            console.log('Sud Ovest: ' + map.getBounds().getSouthWest());
        }
        /** Set current coordinates in a cookie. */
        var map_position = {
            lng: map.getCenter().lng(),
            lat: map.getCenter().lat(),
            zoom: map.getZoom()
        };
        setCookie('map_position', JSON.stringify(map_position), 1);
        /** 
         * If this is the first load or the search checkbox is enabled, than the client launchs an
         * ajax request to the server.
         * */
        if (!first_loaded || $('#search_button').is(':checked')) {
            searchArea(map.getBounds().getNorthEast(), map.getBounds().getSouthWest());
            first_loaded = true;
        }
    });
}
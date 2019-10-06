<script>
    /**
     * @fileOverview Various tool functions to elaborate Queries about diagnoses and pollutions sources.
     * @author <a href="mailto:s@example.com">Simone Mosi</a>
     * @author <a href="mailto:s@example.com">Luca Ioffredo</a>
     * @version 1.0
     */

    /**
     * Debug variable.
     * If TRUE enable each debug print.
     * @type Boolean
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
     * zIndex level for shapes created by google maps.
     * @type {int}
     */
    var zIndex = 100;
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
     * Store all Shapes retrieved by database.
     * Each element is an array contains all Shapes retireved by database. 
     * It is populated at runtime at each request sends by the user.
     * @type {Object[]}
     */
    var tooltipInsertedShapes = {
        'diagnoses': {},
        'pollution_srcs': {}
    };
    /**
     * It rapresents the last event selected by the user.
     * @type {google.maps.drawing.OverlayType.MARKER|CIRCLEPOLYGONPOLYLINE}
     */
    var lastDrawer = null;
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
             * The name of the shape.
             * @type {string}
             */
            this.name = '';
            /**
             * The start-date of the shape.
             * @type {string}
             */
            this.dateFrom = '';
            /**
             * The end-date of the shape.
             * @type {string}
             */
            this.dateTo = '';
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
         * Save the state of this Shape.
         * @param {string} name 
         * Name of this Shape.
         * @param {string} dateFrom 
         * Date in a string format as dd/mm/YYYY or similar.
         * @param {string} dateTo
         * Date in a string format as dd/mm/YYYY or similar.
         * @param {object} icons 
         * @see {@link icons} for further information.
         * An object with attributes complete, pending, edited, delete. It's user defined.
         * @this {Shape}
         * @return {object} Machine-readable representation of this Shape on success, FALSE if the request fails.
         */
        save(name, dateFrom, dateTo, icons) {
            if (!this.overlay) {
                return false;
            }
            this.name = name;
            this.dateFrom = dateFrom;
            this.dateTo = dateTo;
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
                default:
                    return false;
            }
            return {
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

        var handle = $("#custom-handle");
        /**
         * Prepares slider in the form.
         */
        $("#slider").slider({
            min: 0,
            max: 1000,
            create: function () {
                handle.text($(this).slider("value") + ' km');
            },
            slide: function (event, ui) {
                changeRadius(ui.value);
                handle.text(ui.value + ' km');
            },
            change: function (event, ui) {
                handle.text(ui.value + ' km');
            }
        });

        $('#date_from_query, #date_to_query').datepicker({
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
                if (dateObject.id === "date_from_query") {
                    $("#date_to_query").datepicker("option", "minDate", $.datepicker.parseDate(dateObject.settings.dateFormat, dateText));
                } else if (dateObject.id === "date_to_query") {
                    $("#date_from_query").datepicker("option", "maxDate", $.datepicker.parseDate(dateObject.settings.dateFormat, dateText));
                }
            }
        });

        $('.clear-input-text').click(function (event) {
            $(this).parent('div').find('input[type="text"]').each(function () {
                $(this).val('');
            });
            if (!$("#date_from_query").val()) {
                $("#date_to_query").datepicker("option", "minDate", "");
            }
            if (!$("#date_to_query").val()) {
                $("#date_from_query").datepicker("option", "maxDate", "today");
            }
        });
    });

    /**
     * Preprocess each Shape inserted, edited or deleted by the user before submit.
     * @returns {boolean}
     */
    function prepBeforeSubmit() {
        var type = $('#type').val();
        var date_from = $('#date_from_query').val();
        var date_to = $('#date_to_query').val();
        $('#date_from').val(date_from);
        $('#date_to').val(date_to);
        switch (type) {
            case 'circle':
                if (pendingShape !== null) {
                    var range = Math.floor(pendingShape.overlay.getRadius());
                    $('#range_element').val(range);
                    /**
                     * @see {@link Shape#save}
                     * @type Boolean|object|Shape
                     */
                    var shape = pendingShape.save(name, date_from, date_to, icons);
                    if (shape) {
                        insertedShapes[shape.id] = shape;
                    } else {
                        alert("Error circle! Id: " + pendingShape.id);
                    }
                }
                break;
            case 'polygon':
                if (pendingShape !== null) {
                    var range = $('#range_query').val();
                    if (!range || range < 0 || range > 1000) {
                        alert("Error: invalid range");
                        return false;
                    }
                    $('#range_element').val(range);
                    /**
                     * @see {@link Shape#save}
                     * @type Boolean|object|Shape
                     */
                    var shape = pendingShape.save(name, date_from, date_to, icons);
                    if (shape) {
                        insertedShapes[shape.id] = shape;
                    } else {
                        alert("Error polygon! Id: " + pendingShape.id);
                    }
                }
                break;
            case 'diagnosis':
                var range = $('#range_query').val();
                if (!range || range < 0 || range > 1000) {
                    alert("Error: invalid range");
                    return false;
                }
                $('#range_element').val(range);
                break;
            case 'pollution':
                var range = $('#range_query').val();
                if (!range || range < 0 || range > 1000) {
                    alert("Error: invalid range");
                    return false;
                }
                $('#range_element').val(range);
                break;
            default:
                return false;
        }
        if (pendingShape !== null) {
            /**
             * Iterates on each element of {@link insertedShapes} to preprocess data before parsing as JSON.
             * @see {@link toObj} for further information. 
             */
            $.each(insertedShapes, function (key, elem) {
                insertedShapes[key] = insertedShapes[key].toObj();
            });
            if (debug)
                console.log(JSON.stringify(insertedShapes));
            /**
             * Parse each array as JSON string and store them into three input elements.
             */
            $('#shape_area').val(JSON.stringify(insertedShapes));
        }
        window.location.hash = '';
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
            $("#date_from_shape" + id_type + "_id_" + id + ", #date_to_shape" + id_type + "_id_" + id).datepicker({
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
                    if (dateObject.id === "date_from_shape" + id_type + "_id_" + id) {
                        $("#date_to_shape" + id_type + "_id_" + id).datepicker("option", "minDate", $.datepicker.parseDate(dateObject.settings.dateFormat, dateText));
                    } else if (dateObject.id === "date_to_shape" + id_type + "_id_" + id) {
                        $("#date_from_shape" + id_type + "_id_" + id).datepicker("option", "maxDate", $.datepicker.parseDate(dateObject.settings.dateFormat, dateText));
                    }
                }
            });
            var dateFrom = $("#date_from_shape" + id_type + "_id_" + id).val();
            var dateTo = $("#date_to_shape" + id_type + "_id_" + id).val();
            /**
             * After the user changes the date by datepicker this enforces a minimum and a maximum range.
             * This work like onSelect function.
             */
            if (dateFrom) {
                $("#date_to_shape" + id_type + "_id_" + id).datepicker("option", "minDate", $.datepicker.parseDate("dd/mm/yy", dateFrom));
            }
            if (dateTo) {
                $("#date_from_shape" + id_type + "_id_" + id).datepicker("option", "maxDate", $.datepicker.parseDate("dd/mm/yy", dateTo));
            }
        });
    }

    /**
     * Binds a tooltip to the id.
     * @param {type} id
     * 
     */
    function bindTooltip(id) {
        $('#tooltip_shape_id_' + id + '[data-toggle="tooltip"]').tooltip();
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
//			pendingShape.overlay.tooltip.close();
            pendingShape.overlay.setMap(null);
            enableUI(map);
            pendingShape = null;
        }
        $.each(tooltipInsertedShapes['diagnoses'], function (index, value) {
            if (index != id) {
                value['tooltip'].close();
            }
        });
        $.each(tooltipInsertedShapes['pollution_srcs'], function (index, value) {
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

    /**
     * Returns a HTML code string with the content of ToolTip of a overlay created by Google Maps API.
     * @param {int} id
     * @param {string} name
     * @param {string} dateFrom
     * @param {string} dateTo
     * @returns {String}
     */
    function printTooltipForm_Pollutions(id, name = "", dateFrom = "", dateTo = "") {
        var readonly = "readonly";
        var id_type = "_retrieved";
        if (!dateTo) {
            dateTo = "";
        }
        return '<div id="content_shape' + id_type + '_id_' + id + '" class="container container-infowindow-query">' +
                '<h6>Pollution source</h6>' +
                '<div class="row text-center">' +
                '</div>' +
                '<form action="#" class="form form-pollution p-1" method="POST" onsubmit="return false;" autocomplete="off">' +
                '<div class="row mb-2">' +
                '<label class="col-xs-2 col-sm-2 col-md-2 col-lg-2 label-pollution" for="name_pollution_shape' + id_type + '_id_' + id + '">Name:</label>' +
                '<input class="form-control col" placeholder="Name" id="name_pollution_shape' + id_type + '_id_' + id + '" name="name_pollution_shape' + id_type + '_id_' + id + '" value="' + name + '" maxlength="255" required="" type="text" ' + readonly + '>' +
                '</div>' +
                '<div class="row">' +
                '<label class="mb-1 col-4 col-md-2 col-lg-2 label-pollution" for="date_from_shape' + id_type + '_id_' + id + '">From:</label>' +
                '<input class="mb-1 form-control col-8 col-md-4 col-lg-4 datepicker" placeholder="dd/mm/yyyy" id="date_from_shape' + id_type + '_id_' + id + '" name="date_from_shape' + id_type + '_id_' + id + '" value="' + dateFrom + '" required type="text" ' + readonly + '>' +
                '<label class="mb-1 col-4 col-md-2 col-lg-2 label-pollution" for="date_to_shape' + id_type + '_id_' + id + '">To:</label>' +
                '<input class="mb-1 form-control col-8 col-md-4 col-lg-4 datepicker" placeholder="dd/mm/yyyy" id="date_to_shape' + id_type + '_id_' + id + '" name="date_to_shape' + id_type + '_id_' + id + '" value="' + dateTo + '" type="text" ' + readonly + '>' +
                '</div>' +
                '<div class="row">' +
                '<button onclick="saveInfo(\'pollution\',' + id + ');" class="btn btn-outline-info w-100 mt-2 ml-2"><i class="fas fa-search"></i></button>' +
                '</div>' +
                '</form>' +
                '</div>';
    }

    /**
     * Returns a HTML code string with the content of ToolTip of a overlay created by Google Maps API.
     * @param {int} id
     * @param {int} idPathology
     * @param {string} date
     * @returns {String}
     */
    function printTooltipForm_Diagnoses(id, idPathology = false, namePathology = "", date = "") {
        var readonly = "readonly";
        var id_type = "_retrieved";
        return '<div id="content_shape' + id_type + '_id_' + id + '" class="container container-infowindow-query">' +
                '<h6>Diagnosis</h6>' +
                '<div class="row text-center">' +
                '</div>' +
                '<form action="#" class="form form-pollution p-1" method="POST" onsubmit="return false;" autocomplete="off">' +
                '<div class="row mb-2">' +
                '<label class="col-xs-4 col-sm-4 col-md-4 col-lg-4 label-pollution" for="name_pathology' + id_type + '_id_' + id + '">Pathology:</label>' +
                '<input class="form-control col-xs-8 col-sm-8 col-md-8 col-lg-8 col-xl-8" type="text" value="' + namePathology + '" id="name_pathology' + id_type + '_id_' + id + '" name="name_pathology' + id_type + '_id_' + id + '" ' + readonly + '>' +
                '</div>' +
                '<div class="row">' +
                '<label class="mb-1 col-4 col-md-4 col-lg-4 label-pollution" for="date' + id_type + '_id_' + id + '">Date:</label>' +
                '<input class="mb-1 form-control col-8 col-md-8 col-lg-8 col-xl-8 datepicker" placeholder="dd/mm/yyyy" id="date' + id_type + '_id_' + id + '" name="date' + id_type + '_id_' + id + '" value="' + date + '" required type="text" ' + readonly + '>' +
                '</div>' +
                '<div class="row mt-2">' +
                '<label class="col-xs-12 col-sm-12 col-md-3 col-lg-3 label-pollution" for="address' + id_type + '_id_' + id + '">Address:</label>' +
                '<span class="col-xs-8 col-sm-8 col-md-9 col-lg-9 pt-1 m-0" id="address_id_' + id + '">Not retrieved</span>' +
                '</div>' +
                '<div class="row">' +
                '<button onclick="saveInfo(\'diagnosis\',' + id + ');" class="btn btn-outline-info w-100 mt-2 ml-2"><i class="fas fa-search"></i></button>' +
                '</div>' +
                '</form>' +
                '</div>';
    }

    /**
     * SaveInfo make some operation about a Shape.
     * @param {string} op 
     * The value is one of: insert, edit, delete.
     * With insert SaveInfo prepares the {@link pendingShape} in {@link insertedShapes}.
     * With edit SaveInfo prepares the Shape selected in {@link editedShapes}.
     * With delete SaveInfo prepares the Shape selected in {@link deletedShapes}.
     * @param {int|boolean} id
     * @returns {boolean}
     */
    function saveInfo(op, id = false) {
        switch (op) {
            /**
             * This case is only for inserting new shape. It is not for retrieved data.
             */
            case 'diagnosis':
                $('#type').val('diagnosis');
                $('.query-option').addClass('hidden');
                $('.diagnosis-option').removeClass('hidden');
                $('#name_element').val($('#name_pathology_retrieved_id_' + id).val());
                $('#date_from_element').val($('#date_retrieved_id_' + id).val());
                $('#id_element').val(id);
                break;
            case 'pollution':
                $('#type').val('pollution');
                $('.query-option').addClass('hidden');
                $('.pollution-option').removeClass('hidden');
                $('#name_element').val($('#name_pollution_shape_retrieved_id_' + id).val());
                $('#date_from_element').val($('#date_from_shape_retrieved_id_' + id).val());
                $('#date_to_element').val($('#date_to_shape_retrieved_id_' + id).val());
                $('#id_element').val(id);
                break;
        }
        $('html, body').animate({
            scrollTop: $('#options_panel').offset().top
        }, 800, function () {
            window.location.hash = '#options_panel';
        });
        return false;
    }

    /**
     * deleteShape delete the current drawn Shape from the Map.
     * @param {int} id
     * @returns {boolean}
     */
    function deleteShape(id = false) {
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
            if (shape.overlay.hasOwnProperty('tooltip')) {
                shape.overlay.tooltip.close();
            }
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
            $('#type').val('');
            $('.query-option').addClass('hidden');
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
            $('#name_pollution_shape_id_' + pendingShape.id).prop('readonly', true);
            $('#date_from_shape_id_' + pendingShape.id).prop('readonly', true);
            $('#date_to_shape_id_' + pendingShape.id).prop('readonly', true);
            $('#submit_shape_id_' + pendingShape.id).prop('disabled', true);
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
            zoomControl: true,
            scrollwheel: false,
            disableDoubleClickZoom: true,
            disableDefaultUI: true});
        /** Without arguments setDrawingMode() set the current cursor to hand */
        drawingManager.setDrawingMode();
        /** Disable drawing control on the map */
        drawingManager.setOptions({
            drawingControl: false
        });
        $('#floating-panel').removeClass('hidden');
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
        $('#floating-panel').addClass('hidden');
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
        var loaded_pollution = [], loaded_diagnosis = [];
        $.each(tooltipInsertedShapes['diagnoses'], function (key, value) {
            /** 
             * Iterate on each key that rapresent the id of a Shape.
             * */
            loaded_diagnosis.push(key);
        });
        $.each(tooltipInsertedShapes['pollution_srcs'], function (key, value) {
            /** 
             * Iterate on each key that rapresent the id of a Shape.
             * */
            loaded_pollution.push(key);
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
                /**
                 * Parse loaded as a JSON string.
                 */
                'loaded_diagnosis': JSON.stringify(loaded_diagnosis),
                'loaded_pollution': JSON.stringify(loaded_pollution)
            },
            url: 'ajaxArea.php'
        }).done(function (result) {
            var json = jQuery.parseJSON(result);
            if (json) {
                if (json.success === true) {
                    populateMap_Diagnoses(json);
                    populateMap_Pollutions(json);
                    /** Injects the html code retrieved from the server in the div */
//                    $('#result_search').html('<script>' + json.data_found + '<\/script>');
                } else {
                    writeMsg(json.msg, 'danger');
                }
            } else {
                console.log(json);
            }
        }).fail(function (result) {
//			alert("Error: " + result.status);
        }).always(function () {
            /** Disable spinner in the h1 element */
            $('.fa-spinner.fa-spin').addClass('hidden');
        });
    }

    /**
     * Make a polygon on map.
     * @param {object} shape
     * @returns {undefined}     */
    function makePolygon(shape) {
        var coordinates = [];
        $.each(shape.coordinates, function (point, coordinate) {
            coordinates.push({lat: parseFloat(coordinate[1]), lng: parseFloat(coordinate[0])});
        });
        var searched_area = new google.maps.Polygon({
            path: coordinates,
            draggable: false,
            editable: false,
            strokeColor: '#13C800',
            strokeOpacity: 0.8,
            strokeWeight: 4,
            fillColor: '#13C800',
            fillOpacity: 0.25,
            geodesic: false,
            zIndex: 50
        });
        searched_area.setMap(map);
    }

    /**
     * Populates the map with diagnoses from the json.
     * @param {object} json
     * @returns {undefined}     
     */
    function populateMap_Diagnoses(json) {
        /** Diagnoses */
        if (json.data_found.diagnoses) {
            var labels = [];
            var data = [];
            $.each(json.data_found.diagnoses, function (key, values) {
                var id = values.id;
                tooltipInsertedShapes['diagnoses'][id] = {};
                tooltipInsertedShapes['diagnoses'][id]['type'] = 'diagnosis';
                tooltipInsertedShapes['diagnoses'][id]['shape'] = new google.maps.Marker({
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
                tooltipInsertedShapes['diagnoses'][id]['tooltip'] = new google.maps.InfoWindow({
                    content: printTooltipForm_Diagnoses(id, values.id_pathology, values.name, values.date)
                });
                tooltipInsertedShapes['diagnoses'][id]['shape'].addListener('click', function () {
                    if (!tooltipInsertedShapes['diagnoses'].hasOwnProperty(id))
                        return false;
                    tooltipInsertedShapes['diagnoses'][id]['tooltip'].open(map, tooltipInsertedShapes['diagnoses'][id]['shape']);
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
                bindDatepicker(tooltipInsertedShapes['diagnoses'][id]['tooltip'], id, true);

                /**
                 * This condition is true only after a submit in the search form.
                 */
                if (values.hasOwnProperty('shape_expanded')) {
                    makePolygon(values.shape_expanded);
                }
                /**
                 * This condition is true only after a submit in the search form and
                 * only when there are pathology occurences.
                 */
                if (values.hasOwnProperty('pathology_occurrence')) {
                    populateStatisticTable(values.id_pathology, values.name, values.pathology_occurrence);
                    if (!labels.includes(values.name)) {
                        labels.push(values.name);
                        data.push(parseInt(values.pathology_occurrence));
                    }
                }
            });
            if (labels) {
                prepareGraphic(labels, data);
            }
        }
    }

    /**
     * Populates the map with pollution sources from the json.
     * @param {object} json
     * @returns {undefined}     
     */
    function populateMap_Pollutions(json) {
        /** Pollutions srcs */
        if (json.data_found.pollution_srcs) {
            $.each(json.data_found.pollution_srcs, function (key, values) {
                var id = values.id;
                tooltipInsertedShapes['pollution_srcs'][id] = {};
                tooltipInsertedShapes['pollution_srcs'][id]['type'] = 'pollution_src';
                var coordinates = [];
                $.each(values.shape.coordinates, function (point, coordinate) {
                    coordinates.push({lat: parseFloat(coordinate[1]), lng: parseFloat(coordinate[0])});
                });
                switch (values.shape.type) {
                    case 'POINT':
                        tooltipInsertedShapes['pollution_srcs'][id]['shape'] = new google.maps.Marker({
                            position: coordinates[0],
                            map: map,
                            draggable: false,
                            editable: false,
                            icon: {
                                url: icons.pollution.icon,
                                scaledSize: new google.maps.Size(30, 30), // scaled size
                            },
                            title: values.name
                        });
                        tooltipInsertedShapes['pollution_srcs'][id]['tooltip'] = new google.maps.InfoWindow({
                            content: printTooltipForm_Pollutions(id, values.name, values.date_from, values.date_to)
                        });
                        tooltipInsertedShapes['pollution_srcs'][id]['shape'].addListener('click', function () {
                            if (!tooltipInsertedShapes['pollution_srcs'].hasOwnProperty(id))
                                return false;
                            tooltipInsertedShapes['pollution_srcs'][id]['tooltip'].open(map, tooltipInsertedShapes['pollution_srcs'][id]['shape']);
                            onlyOneTooltip(id, true);
                        });
                        break;
                    case 'LINESTRING':
                        tooltipInsertedShapes['pollution_srcs'][id]['shape'] = new google.maps.Polyline({
                            path: coordinates,
                            draggable: false,
                            editable: false,
                            strokeColor: map_colors.line.strokeColor,
                            strokeOpacity: map_colors.line.strokeOpacity,
                            strokeWeight: map_colors.line.strokeWeight,
                            fillColor: map_colors.line.fillColor,
                            fillOpacity: map_colors.line.fillOpacity,
                            zIndex: 999
                        });
                        tooltipInsertedShapes['pollution_srcs'][id]['shape'].setMap(map);
                        tooltipInsertedShapes['pollution_srcs'][id]['tooltip'] = new google.maps.InfoWindow({
                            content: printTooltipForm_Pollutions(id, values.name, values.date_from, values.date_to)
                        });
                        google.maps.event.addListener(tooltipInsertedShapes['pollution_srcs'][id]['shape'], 'click', function (event) {
                            if (!tooltipInsertedShapes['pollution_srcs'].hasOwnProperty(id))
                                return false;
                            /** Set position of the map on the linestring's boundary */
                            tooltipInsertedShapes['pollution_srcs'][id]['tooltip'].setPosition(event.latLng);
                            tooltipInsertedShapes['pollution_srcs'][id]['tooltip'].open(map);
                            onlyOneTooltip(id, true);
                        });
                        break;
                    case 'POLYGON':
                        tooltipInsertedShapes['pollution_srcs'][id]['shape'] = new google.maps.Polygon({
                            path: coordinates,
                            draggable: false,
                            editable: false,
                            strokeColor: map_colors.polygon.strokeColor,
                            strokeOpacity: map_colors.polygon.strokeOpacity,
                            strokeWeight: map_colors.polygon.strokeWeight,
                            fillColor: map_colors.polygon.fillColor,
                            fillOpacity: map_colors.polygon.fillOpacity,
                            geodesic: false,
                            zIndex: zIndex++
                        });
                        tooltipInsertedShapes['pollution_srcs'][id]['shape'].setMap(map);
                        tooltipInsertedShapes['pollution_srcs'][id]['tooltip'] = new google.maps.InfoWindow({
                            content: printTooltipForm_Pollutions(id, values.name, values.date_from, values.date_to)
                        });
                        google.maps.event.addListener(tooltipInsertedShapes['pollution_srcs'][id]['shape'], 'click', function (event) {
                            if (!tooltipInsertedShapes['pollution_srcs'].hasOwnProperty(id))
                                return false;
                            /** Set position of the map on the polygon's boundary */
                            tooltipInsertedShapes['pollution_srcs'][id]['tooltip'].setPosition(event.latLng);
                            tooltipInsertedShapes['pollution_srcs'][id]['tooltip'].open(map);
                            onlyOneTooltip(id, true);
                        });
                        break;
                }
                bindDatepicker(tooltipInsertedShapes['pollution_srcs'][id]['shape'], id, true);
                /**
                 * This condition is true only after a submit in the search form.
                 */
                if (values.hasOwnProperty('shape_expanded')) {
                    makePolygon(values.shape_expanded);
                    $('#title_pollution_name').text("Diagnoses near '" + values.name + "'");
                }
            });
        }
    }

    /**
     * Populated the statistic's table with the pathologies returned from the server.
     * @param {int} id
     * @param {string} name
     * @param {int} occurences
     * @returns {undefined}     
     */
    function populateStatisticTable(id, name, occurences) {
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
            table_pathologies.row.add([id, name, occurences]).draw();
            lastInsertedIdStatistic[id] = id;
        }
    }

    /**
     * Changes the slider's value when the user interact with the circle.
     * @param {float} value
     * @returns {undefined}     
     */
    function changeSlider(value) {
        if (value < 0) {
            value = 0;
        }
        $("#slider").slider("option", "value", parseInt(value) / 1000);
    }

    /**
     * Changes the circle's radius when the user interact with the slider.
     * @param {int} value
     * @returns {undefined}     
     */
    function changeRadius(value) {
        if (pendingShape && pendingShape.overlay) {
            if (value < 0) {
                value = 0;
            }
            pendingShape.overlay.setRadius(value * 1000);
        }
    }

    function dynamicColors() {
        var r = Math.floor(Math.random() * 255);
        var g = Math.floor(Math.random() * 255);
        var b = Math.floor(Math.random() * 255);
        return "rgb(" + r + "," + g + "," + b + ")";
    }
    ;

    function prepareGraphic(labels, data) {
        var colors = [];
        for (var i = 0; i < labels.length; i++) {
            colors.push(dynamicColors());
        }
        var ctx_1 = document.getElementById("diagnoses_graphic_pie").getContext('2d');
        var myChart = new Chart(ctx_1, {
            type: 'pie',
            data: {
                datasets: [{
                        data: data,
                        backgroundColor: colors,
                        label: 'Diagnoses'
                    }],
                labels: labels
            },
            options: {
                responsive: true
            }
        });


        var ctx_2 = document.getElementById("diagnoses_graphic_bar").getContext('2d');
        var myChart = new Chart(ctx_2, {
            type: 'bar',
            data: {
                datasets: [{
                        data: data,
                        backgroundColor: colors,
                        label: 'Diagnoses'
                    }],
                labels: labels
            },
            options: {
                responsive: true,
                legend: {display: false},
                scales: {
                    yAxes: [{
                            ticks: {
                                autoSkip: true,
                                min: 0
                            }
                        }]
                }
            }
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
         * @type google.maps.LatLng
         */
        var myLatlng = null;
        var last_zoom = 9;
        var last_position = getCookie('map_position');
        if (last_position) {
            last_position = JSON.parse(last_position);
            myLatlng = new google.maps.LatLng(parseFloat(last_position.lat), parseFloat(last_position.lng));
            last_zoom = parseInt(last_position.zoom);
            if (last_zoom < 4) {
                last_zoom = 4;
            }
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
                drawingModes: ['circle', 'polygon']
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
            }
        });

        /** Without arguments setDrawingMode() set the current cursor to hand */
        drawingManager.setDrawingMode();
        /** Set drawingManager on the map */
        drawingManager.setMap(map);

<?php
/**
 * This two conditional populate the map with all data retrieved from the server after a submit.
 */
if (isset($global_shapes)) {
    if (isset($global_shapes['circle'])) {
        ?>
                var query_circle = new google.maps.Circle({
                    strokeColor: '#FF0000',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: '#FF0000',
                    fillOpacity: 0.35,
                    map: map,
                    center: {lat: <?php echo $global_shapes['circle']['lat']; ?>, lng: <?php echo $global_shapes['circle']['lng']; ?>},
                    radius: <?php echo $global_shapes['circle']['radius']; ?>,
                    zIndex: 1
                });
        <?php
    } elseif (isset($global_shapes['polygon'])) {
        $coordinates = array();
        foreach ($global_shapes['polygon'] as $point) {
            $coordinates[] = "{lat: $point[lat], lng: $point[lng]}";
        }
        ?>
                var query_polygon = new google.maps.Polygon({
                    paths: [<?php echo implode(",", $coordinates); ?>],
                    strokeColor: '#FF0000',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: '#FF0000',
                    fillOpacity: 0.35,
                    zIndex: 1
                });
                query_polygon.setMap(map);
        <?php
    }
    if (isset($global_shapes['diagnoses'])) {
        ?>
                /** Diagnoses */
                var json_diagnoses = {
                    'data_found': {
                        'diagnoses': <?php echo json_encode($global_shapes['diagnoses']); ?>
                    }
                };
                populateMap_Diagnoses(json_diagnoses);
        <?php
    }
    if (isset($global_shapes['pollution_srcs'])) {
        ?>
                /** Pollution srcs */
                var json_pollution_srcs = {
                    'data_found': {
                        'pollution_srcs': <?php echo json_encode($global_shapes['pollution_srcs']); ?>
                    }
                };
                populateMap_Pollutions(json_pollution_srcs);
        <?php
    }
    ?>
            first_loaded = true;
    <?php
}
?>

        /**
         * Attach a listener to drawingManager when the overlaycomplete event is fired.
         * overlaycomplete event is fired when the user drawn a shape on the map.
         */
        google.maps.event.addListener(drawingManager, 'overlaycomplete', function (event) {
            /**
             * The object created by overlaycomplete event of Google Maps API.
             * @type {object}
             */
            var newShape = null;
            switch (event.type) {
                case google.maps.drawing.OverlayType.CIRCLE:
                    newShape = event.overlay;
                    shape = newShape;
                    /** Generates a new id */
                    newShape.id_custom = counter.newId();
                    changeSlider(newShape.getRadius());
                    $('.query-option').addClass('hidden');
                    $('.circle-option').removeClass('hidden');
                    /** Store the new Shape as a {@link pendingShape}*/
                    pendingShape = new Shape(newShape.id_custom, event.type, newShape);
                    $('#type').val('circle');
                    break;
                case google.maps.drawing.OverlayType.POLYGON:
                    if (debug)
                        console.log("Insert " + event.type + ":");
                    newShape = event.overlay;
                    shape = newShape;
                    /** Generates a new id */
                    newShape.id_custom = counter.newId();
                    /** Store the new Shape as a {@link pendingShape}*/
                    pendingShape = new Shape(newShape.id_custom, event.type, newShape);
                    $('.query-option').addClass('hidden');
                    $('.polygon-option').removeClass('hidden');
                    $('#type').val('polygon');
                    break;
                default:
                    console.log("Insert default:");
                    console.log(event);
            }
            /** Store last drawning mode used by the user */
            lastDrawer = event.type;
            /** Disable any interaction of the user */
            disableUI(map);
            onlyOneTooltip();
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
</script>
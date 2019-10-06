/** 
 * Folder base of icons.
 * @constant
 * @type {string}
 * */
const iconBase = '../img/';
/** @namespace */
var icons = {
    /** 
     * Object of "complete" state.
     * Contains a path to an icon.
     * It is used to set a marker in Google Maps API.
     * @type {string}
     * */
    complete: {
        icon: iconBase + 'marker-complete.png'
    },
    /** 
     * Object of "pending" state.
     * Contains a path to an icon.
     * It is used to set a marker in Google Maps API.
     * @type {string}
     * */
    pending: {
        icon: iconBase + 'marker-pending.png'
    },
    /** 
     * Object of "deleted" state.
     * Contains a path to an icon.
     * It is used to set a marker in Google Maps API.
     * @type {string}
     * */
    deleted: {
        icon: iconBase + 'marker-deleted.png'
    },
    /** 
     * Object of "edited" state.
     * Contains a path to an icon.
     * It is used to set a marker in Google Maps API.
     * @type {string}
     * */
    edited: {
        icon: iconBase + 'marker-edited.png'
    },
    /** 
     * Object of "edited" state.
     * Contains a path to an icon.
     * It is used to set a marker in Google Maps API.
     * @type {string}
     * */
    diagnosis: {
        icon: iconBase + 'marker-diagnosis.png'
    },
    /** 
     * Object of "edited" state.
     * Contains a path to an icon.
     * It is used to set a marker in Google Maps API.
     * @type {string}
     * */
    pollution: {
        icon: iconBase + 'marker-pollution.png'
    }
};

/** @namespace */
var map_colors = {
    polygon: {
        strokeColor: '#21CCCA',
        strokeOpacity: 0.8,
        strokeWeight: 3,
        fillColor: '#21CCCA',
        fillOpacity: 0.25,
    },
    line: {
        strokeColor: '#21CCCA',
        strokeOpacity: 0.8,
        strokeWeight: 8,
        fillColor: '#21CCCA',
        fillOpacity: 0.25,
    }
};
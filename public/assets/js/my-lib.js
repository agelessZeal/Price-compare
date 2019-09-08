var isMobile = false;
var osStatus = 'unknown';
/**
 * Get Element of top and Left
 **/
var cumulativeOffset = function (element) {
    var top = 0, left = 0;
    do {
        top += element.offsetTop || 0;
        left += element.offsetLeft || 0;
        element = element.offsetParent;
    } while (element);

    return {
        top: top,
        left: left
    };
};
/**
 * Get Mobile Operating System Information
 **/
function getMobileOperatingSystem() {
    var userAgent = navigator.userAgent || navigator.vendor || window.opera;

    // Windows Phone must come first because its UA also contains "Android"
    if (/windows phone/i.test(userAgent)) {
        return "Windows Phone";
    }
    if (/android/i.test(userAgent)) {
        return "Android";
    }
    // iOS detection from: http://stackoverflow.com/a/9039885/177710
    if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
        return "iOS";
    }
    return "unknown";
}
osStatus = getMobileOperatingSystem();
if (osStatus === 'Android' || osStatus === 'iOS') isMobile = true;

/**
 * Get time amount to time formats
 **/
var formatSecondsAsTime = function (secs, format) {
    var hr = Math.floor(secs / 3600);
    var min = Math.floor((secs - (hr * 3600)) / 60);
    var sec = Math.floor(secs - (hr * 3600) - (min * 60));

    if (min < 10) {
        min = "0" + min;
    }
    if (sec < 10) {
        sec = "0" + sec;
    }

    return min + ':' + sec;
};
/**
 * Get Random Real Number
 **/
function getRandomArbitrary(min, max) {
    return Math.random() * (max - min) + min;
}

/**
 * Return random integer within specify range
 * @param min
 * @param max
 * @returns {*}
 */
function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

/**
 * Return random key array(len) within specify range
 * @param min
 * @param max
 * @param len
 * @returns {*}
 * Note, Jquery Needed in this function
 */
function getRandomKeyArray(min, max, len) {

    var tempArr = [];

    if((max-min)<len) len = max-min;

    while (tempArr.length < len) {
        var curKey = Math.floor(Math.random() * (max - min + 1)) + min;
        if (!contains.call(tempArr, curKey)) {
            tempArr.push(curKey);
        }
    }
    return tempArr;
}

/***
 *
 * @param needle
 * @returns {boolean}
 * https://stackoverflow.com/questions/1181575/determine-whether-an-array-contains-a-value
 * var myArray = [0,1,2],
 *     needle = 1,
 *     index = contains.call(myArray, needle); // true
 */
var contains = function (needle) {
    // Per spec, the way to identify NaN is that it is not equal to itself
    var findNaN = needle !== needle;
    var indexOf;

    if (!findNaN && typeof Array.prototype.indexOf === 'function') {
        indexOf = Array.prototype.indexOf;
    } else {
        indexOf = function (needle) {
            var i = -1, index = -1;

            for (i = 0; i < this.length; i++) {
                var item = this[i];

                if ((findNaN && item !== item) || item === needle) {
                    index = i;
                    break;
                }
            }

            return index;
        };
    }

    return indexOf.call(this, needle) > -1;
};

/**
 * Get Random Array with random key array
 * @param myArray
 * @param len
 * @returns {Array}
 */

function getRandomSubArray(myArray, len) {

    var randomKeys = getRandomKeyArray(0, (myArray.length - 1), len);
    var tempArr = [];
    for (var i = 0; i < randomKeys.length; i++) {
        tempArr.push(myArray[i]);
    }
    return tempArr;
}

function extractValueStringFromtObject(myObj, separator) {

    var objStr = "";
    var valueOrder = 0;
    for (var key in myObj) {
        if (valueOrder === 0) {
            objStr += myObj[key]

        } else {
            objStr += separator + myObj[key];
        }
        valueOrder ++;
    }
    return objStr;
}
function openViewDetail(sku) {

    window.open(
        productViewURL+'?cur_sku='+ sku,
        '_blank' // <- This is what makes it open in a new window.
    );
}

String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
};
function Orbit() {
    var $orbit = $("<ul/>").addClass("orbit").appendTo(this);
    var orbitTimer;

    function activeSlide(index, speed, interval_) {
        var interval;
        var $pre;
        var $slide;

        interval = interval_ || 5000;
        clearTimeout(orbitTimer);

        $pre = $orbit.find("li.active");
        if ($orbit.find("li").length === 0) {
            return;
        } else if (index === -1) { // 뒤로
            $slide = $pre.prev("li");
            if ($slide.length === 0) {
                $slide = $orbit.find("li").last();
            }
        } else if (index === -2) { // 앞으로
            $slide = $pre.next("li");
            if ($slide.length === 0) {
                $slide = $orbit.find("li").first();
            }
        } else if (index >= 0) {
            $slide = $orbit.find("li").eq(index);
        } else {
            $slide = $orbit.find("li").first();
        }

        $slide.addClass("active");
        if ($pre.length === 0) {
            $slide.css("display", "list-item");
        } else if ($pre.get(0) !== $slide.get(0)) {
            $pre.removeClass("active");
            $slide.css("z-index", 2);
            $slide.fadeIn(speed, function () {
                $pre.css("z-index", 0);
                $pre.css("display", "none");
                $slide.css("z-index", 1);
            });
        }

        // 타이머
        if (interval > 0) {
            orbitTimer = setTimeout(function () { activeSlide(-2, "slow", interval); }, interval);
        }
    }

    function addButton(text, direction) {
        var $button = $("<button/>")
            .addClass(direction)
            .text(text)
            .appendTo($orbit);

        // 버튼 클릭 이벤트
        var index = null;
        if (direction === "left") {
            index = -1;
        } else if (direction === "right") {
            index = -2;
        }
        if (index != null) {
            $button.on("click", function () {
                activeSlide(index, "fast");
            });
        }
    }

    function applyData(data) {
        var datum;
        var $slide;
        var summary;
        var description;
        var url;
        var $wrapper;
        var $caption;
        var i;

        for (i = 0; i < data.length; i++) {
            datum = data[i];
            $slide = $("<li/>")
                .css("background-image", "url(\"" + datum.Image + "\")")
                .css("background-position", datum.Position)
                .appendTo($orbit);

            summary = datum.Summary || "";
            description = datum.Description || "";
            url = datum.URL || "";
            if (summary.length > 0 || description.length > 0) {
                $wrapper = $("<div/>").addClass("caption-wrapper").appendTo($slide);
                $caption = $("<div/>").addClass("caption").appendTo($wrapper);

                if (summary.length > 0) {
                    $("<div/>").addClass("summary").text(summary).appendTo($caption);
                }
                if (description.length > 0) {
                    $("<div/>").addClass("description").text(description).appendTo($caption);
                }
            }
            if (url.length > 0) {
                $slide.css("cursor", "pointer");
                $slide.on("click", { url: url }, function (e) {
                    window.open(e.data.url);
                });
            } else {
                $slide.css("cursor", "unset");
            }
        }
    }

    this.load = function load(data) {
        applyData(data);

        if (data.length > 1) {
            addButton("<", "left");
            addButton(">", "right");
            activeSlide(0, "slow");
        } else {
            activeSlide(0, 0, 0);
        }
    };

    this.stop = function stop() {
        clearTimeout(orbitTimer);
    };

    this.kill = function kill() {
        this.stop();
        $orbit.remove();
    };
}

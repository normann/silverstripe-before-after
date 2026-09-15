/**
 * Before/After image comparison slider.
 *
 * Framework-free: no jQuery or lodash dependency, so this can be loaded as a
 * plain <script> via SilverStripe's Requirements API on any project.
 */
(function () {
    'use strict';

    function throttle(fn, wait) {
        var lastCall = 0;
        var timeout = null;
        return function () {
            var context = this;
            var args = arguments;
            var now = Date.now();
            var remaining = wait - (now - lastCall);

            if (remaining <= 0) {
                clearTimeout(timeout);
                timeout = null;
                lastCall = now;
                fn.apply(context, args);
            } else if (!timeout) {
                timeout = setTimeout(function () {
                    lastCall = Date.now();
                    timeout = null;
                    fn.apply(context, args);
                }, remaining);
            }
        };
    }

    function debounce(fn, wait) {
        var timeout = null;
        return function () {
            var context = this;
            var args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function () {
                fn.apply(context, args);
            }, wait);
        };
    }

    var BeforeAfterImage = function (wrapper) {
        this._onStartDrag = this.startDrag.bind(this);
        this._onStopDrag = this.stopDrag.bind(this);
        this._onDrag = throttle(this.onDrag.bind(this), 10);

        this._addHover = null;
        this._removeHover = null;

        this.wrapper = wrapper;
        this.slider = wrapper.querySelector('.before-after-slider');
        this.handler = wrapper.querySelector('.handler');
        this.handlerStyle = wrapper.querySelector('.handler-style');
        this.handlerStyleTriangles = wrapper.querySelector('.handler-style-triangles');
        this.before = wrapper.querySelector('.before-after-slider-image.before');
        this.after = wrapper.querySelector('.before-after-slider-image.after');
        this.imagecaption = wrapper.querySelector('.before-after-caption');
        this.direction = this.slider.dataset.direction || 'horizontal';
        this.imagecatptions = JSON.parse(this.slider.dataset.imagecatptions);
        this.imageratio = JSON.parse(this.slider.dataset.imageratio);
        this.offset = parseInt(this.slider.dataset.offset, 10) || 50;
        this.touchSupported = window.matchMedia('(pointer: coarse)').matches;

        this.handlerSet = [this.handler, this.handlerStyle, this.handlerStyleTriangles];

        this.init();
    };

    BeforeAfterImage.prototype.init = function () {
        this.setSliderDivHeight();
        this.setOffset(this.offset);

        this.registerSliderBehavior();
        this.registerHoverToggleLabels();
    };

    BeforeAfterImage.prototype.setOffset = function (percent) {
        if (this.direction === 'vertical') {
            this.before.style.clipPath = 'inset(0 0 ' + (100 - percent) + '% 0)';
            this.after.style.clipPath = 'inset(' + percent + '% 0 0 0)';
            this.handler.style.top = percent + '%';
        } else if (this.direction === 'diagonalLeft') {
            this.handler.style.left = percent + '%';
            this.handler.style.top = (100 - percent) + '%';

            if (percent < 50) {
                var diagonalP = (50 - percent) * 2;
                var diagonalP2 = percent * 2;
                this.before.style.clipPath = 'polygon(0 ' + diagonalP + '%, ' + diagonalP2 + '% 100%, 0 100%)';
                this.after.style.clipPath = 'polygon(0 0, 100% 0, 100% 100%, ' + diagonalP2 + '% 100%, 0 ' + diagonalP + '%)';
            } else if (percent === 50) {
                this.before.style.clipPath = 'polygon(0 0, 100% 100%, 0 100%)';
                this.after.style.clipPath = 'polygon(100% 0, 100% 100%, 0 0)';
            } else {
                var dp = (percent - 50) * 2;
                var dp2 = (100 - percent) * 2;
                this.before.style.clipPath = 'polygon(0 0, ' + dp + '% 0, 100% ' + dp2 + '%, 100% 100%, 0 100%)';
                this.after.style.clipPath = 'polygon(100% 0, 100% ' + dp2 + '%, ' + dp + '% 0)';
            }
        } else if (this.direction === 'diagonalRight') {
            this.handler.style.left = percent + '%';
            this.handler.style.top = percent + '%';
            if (percent < 50) {
                var diagP = percent * 2;
                this.before.style.clipPath = 'polygon(0 0,' + diagP + '% 0, 0 ' + diagP + '%)';
                this.after.style.clipPath = 'polygon(' + diagP + '% 0, 100% 0, 100% 100%, 0 100%, 0 ' + diagP + '%)';
            } else if (percent === 50) {
                this.before.style.clipPath = 'polygon(0 0, 100% 0, 0 100%)';
                this.after.style.clipPath = 'polygon(100% 0, 100% 100%, 0 100%)';
            } else {
                var diagP2 = (percent - 50) * 2;
                this.before.style.clipPath = 'polygon(0 0, 100% 0, 100% ' + diagP2 + '%,  ' + diagP2 + '% 100%, 0 100%)';
                this.after.style.clipPath = 'polygon(100% ' + diagP2 + '%, 100% 100%, ' + diagP2 + '% 100%)';
            }
        } else {
            this.before.style.clipPath = 'inset(0 ' + (100 - percent) + '% 0 0)';
            this.after.style.clipPath = 'inset(0 0 0 ' + percent + '%)';
            this.handler.style.left = percent + '%';
        }

        var offset = this.offset;

        if (offset > 50 && percent <= 50) {
            this.imagecaption.innerHTML = this.imagecatptions.after;
        }

        if (offset <= 50 && percent > 50) {
            this.imagecaption.innerHTML = this.imagecatptions.before;
        }

        // This is an initial call
        if (offset === percent) {
            if (percent > 50) {
                this.imagecaption.innerHTML = this.imagecatptions.before;
            } else {
                this.imagecaption.innerHTML = this.imagecatptions.after;
            }
        }

        this.offset = percent;
    };

    BeforeAfterImage.prototype.setSliderDivHeight = function () {
        var rect = this.slider.getBoundingClientRect();
        this.slider.style.height = (this.imageratio.height * (rect.width / this.imageratio.width)) + 'px';
    };

    BeforeAfterImage.prototype.registerSliderBehavior = function () {
        if (this.touchSupported) {
            this.handler.addEventListener('touchstart', this._onStartDrag, { passive: false });
            document.addEventListener('touchend', this._onStopDrag);
            document.addEventListener('touchmove', this._onDrag, { passive: false });
        } else {
            this.handler.addEventListener('mousedown', this._onStartDrag);
            document.addEventListener('mouseup', this._onStopDrag);
            document.addEventListener('mousemove', this._onDrag);
        }

        this.dragging = false;
    };

    BeforeAfterImage.prototype.registerHoverToggleLabels = function () {
        var self = this;

        if (this.slider.classList.contains('label-show-onMouseOver')) {
            this._addHover = function (e) {
                if (
                    (e.type === 'mouseover' && e.toElement === self.slider && self.handlerSet.indexOf(e.fromElement) === -1)
                    || e.type === 'touchstart'
                ) {
                    if (!self.slider.classList.contains('hover-active')) {
                        self.slider.classList.add('hover-active');
                    }
                }
            };
            this._removeHover = debounce(function (e) {
                if (
                    (e.type === 'mouseout' || (e.fromElement === self.slider && self.handlerSet.indexOf(e.toElement) === -1))
                    || e.type === 'touchend'
                ) {
                    if (self.slider.classList.contains('hover-active')) {
                        self.slider.classList.remove('hover-active');
                    }
                }
            }, 100);

            if (this.touchSupported) {
                this.slider.addEventListener('touchstart', this._addHover);
                this.slider.addEventListener('touchend', this._removeHover);
            } else {
                this.slider.addEventListener('mouseover', this._addHover);
                this.slider.addEventListener('mouseout', this._removeHover);
            }
        }
    };

    BeforeAfterImage.prototype.startDrag = function (e) {
        e.preventDefault();
        this.dragging = true;
    };

    BeforeAfterImage.prototype.stopDrag = function (e) {
        e.preventDefault();
        this.dragging = false;
    };

    BeforeAfterImage.prototype.onDrag = function (e) {
        if (!this.dragging) return;
        var clientX, clientY;
        if (e.touches) {
            clientX = e.touches[0].clientX;
            clientY = e.touches[0].clientY;
        } else {
            clientX = e.clientX;
            clientY = e.clientY;
        }

        var rect = this.slider.getBoundingClientRect();

        var percent;
        if (this.direction === 'vertical') {
            percent = ((clientY - rect.top) / rect.height) * 100;
        } else {
            percent = ((clientX - rect.left) / rect.width) * 100;
        }

        percent = Math.max(0, Math.min(100, percent));

        this.setOffset(percent);
    };

    BeforeAfterImage.prototype.destroy = function () {
        if (this.touchSupported) {
            this.handler.removeEventListener('touchstart', this._onStartDrag);
            document.removeEventListener('touchend', this._onStopDrag);
            document.removeEventListener('touchmove', this._onDrag);
        } else {
            this.handler.removeEventListener('mousedown', this._onStartDrag);
            document.removeEventListener('mouseup', this._onStopDrag);
            document.removeEventListener('mousemove', this._onDrag);
        }

        if (this.slider.classList.contains('label-show-onMouseOver')) {
            if (this.touchSupported) {
                this.slider.removeEventListener('touchstart', this._addHover);
                this.slider.removeEventListener('touchend', this._removeHover);
            } else {
                this.slider.removeEventListener('mouseover', this._addHover);
                this.slider.removeEventListener('mouseout', this._removeHover);
            }
        }

        this.slider.classList.remove('hover-active');
    };

    var allSliders = [];
    var resizeObserver = window.ResizeObserver ? new ResizeObserver(function () {
        allSliders.forEach(function (slider) {
            slider.setSliderDivHeight();
            slider.setOffset(slider.offset);
        });
    }) : null;

    var globalResizeHandler = throttle(function () {
        allSliders.forEach(function (slider) {
            slider.setSliderDivHeight();
            slider.setOffset(slider.offset);
        });
    }, 200);

    function initSlider(wrapper) {
        var slider = new BeforeAfterImage(wrapper);
        allSliders.push(slider);

        if (resizeObserver) {
            resizeObserver.observe(wrapper);
        }
    }

    function lazyInitSliders() {
        var wrappers = document.querySelectorAll('.before-after-slider-wrapper');

        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        var wrapper = entry.target;
                        initSlider(wrapper);
                        obs.unobserve(wrapper);
                    }
                });
            }, {
                rootMargin: '100px'
            });

            wrappers.forEach(function (wrapper) {
                observer.observe(wrapper);
            });
        } else {
            wrappers.forEach(function (wrapper) {
                initSlider(wrapper);
            });
        }

        window.addEventListener('resize', globalResizeHandler);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', lazyInitSliders);
    } else {
        lazyInitSliders();
    }

    window.BeforeAfterImage = BeforeAfterImage;
})();

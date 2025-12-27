const { expect } = require('chai');
const jsdom = require('jsdom');

// /workspaces/fandom-app/skins/common/form.test.js

const { JSDOM } = jsdom;

// Helper to load jQuery and the script under test
function setupDOM(html) {
    const dom = new JSDOM(html, { runScripts: "dangerously", resources: "usable" });
    const window = dom.window;
    const document = window.document;

    // Load jQuery
    const script = document.createElement('script');
    script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
    document.head.appendChild(script);

    return new Promise((resolve) => {
        script.onload = () => {
            // Patch jQuery with .exists()
            window.$.fn.exists = function() { return this.length > 0; };

            // Inject the code under test
            const testedScript = document.createElement('script');
                require('path').resolve(__dirname, 'form.js'), 'utf8'
            );
            document.head.appendChild(testedScript);

            testedScript.onload = () => resolve({ window, document, $: window.$ });
            // For inline script, resolve immediately
            resolve({ window, document, $: window.$ });
        };
    });
}

describe('form.js', function() {
    let window, document, $;

    beforeEach(async function() {
        const html = `
            <form class="highlightform">
                <div class="formblock"><input type="text" id="a"></div>
                <div class="formblock"><input type="text" id="b"></div>
                <div class="formhighlight" style="display:none;position:absolute;"></div>
            </form>
        `;
        const env = await setupDOM(html);
        window = env.window;
        document = env.document;
        $ = env.$;
    });

    afterEach(function() {
        window.close();
    });

    it('should add "selected" class to first .formblock on load', function() {
        expect($('.formblock').first().hasClass('selected')).to.be.true;
    });

    it('should move "selected" class on focus', function(done) {
        const $first = $('.formblock').first();
        const $second = $('.formblock').eq(1);
        const $inputSecond = $second.find('input');

        // Simulate focus event
        $inputSecond.trigger('focus');

        // Wait for animation (simulate, since jsdom doesn't animate)
        setTimeout(() => {
            expect($first.hasClass('selected')).to.be.false;
            expect($second.hasClass('selected')).to.be.true;
            done();
        }, 10);
    });

    it('should not change selection if already selected', function(done) {
        const $first = $('.formblock').first();
        const $inputFirst = $first.find('input');

        $inputFirst.trigger('focus');

        setTimeout(() => {
            expect($first.hasClass('selected')).to.be.true;
            done();
        }, 10);
    });

    it('should not throw if .formblock does not exist', function(done) {
        // Add an input outside any .formblock
        $('.highlightform').append('<input type="text" id="outside">');
        $('#outside').trigger('focus');

        setTimeout(() => {
            // No error, test passes
            expect($('.formblock.selected').length).to.equal(1);
            done();
        }, 10);
    });

    it('should show and hide .formhighlight during selection change', function(done) {
        const $highlight = $('.formhighlight');
        const $inputSecond = $('.formblock').eq(1).find('input');

        $inputSecond.trigger('focus');

        setTimeout(() => {
            // After animation, highlight should be hidden
            expect($highlight.css('display')).to.match(/none|hidden/i);
            done();
        }, 10);
    });
});
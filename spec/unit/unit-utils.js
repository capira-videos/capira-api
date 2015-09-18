'use strict';
var utils=require('../test-utils.js');


utils.basicExpectationsOnUnitTypes = function(error, response, body) {
    expect(response.statusCode).toBe(200);

    // Test Types 
    expect(body).toBeObject();
    expect(body.id).toBeNumber();
    expect(body.parent).toBeNumber();
}

utils.fullExpectationsOnUnitTypes = function(error, response, body) {
    utils.basicExpectationsOnChannelTypes(error, response, body);
    expect(body.title).toBeString();
    expect(body.overlays).toBeArray();
}


utils.basicExpectationsOnUnit = function(error, response, body,requested) {
    utils.basicExpectationsOnUnitTypes(error, response, body);
    utils.expectationsOnData(requested, body);
}

utils.fullExpectationsOnUnit = function(error, response, body, requested) {
    utils.fullExpectationsOnUnitTypes(error, response, body);
    utils.expectationsOnData(requested, body);
}

utils.unitFactory.getUnit = function() {
    return {};
}

utils.unitFactory.getUnitWithParent = function(parent) {
    return {
    "overlays": [{
        "id": 1,
        "type": "standard-annotation",
        "heading": "Capira Socrates Quiz Showcase",
        "body": "<h3>Welcome to the first preview of Capira Socrates!</h3>We made this preview to demonstrate the basic functionality.<br/><br/>We would appreciate feedback, to make sure that we are building what instructors need and students love.<br/><br/>Click the play button to go on!",
        "reaction": {
            "type": "showOverlay",
            "target": 0
        }
    }, {
        "id": 0,
        "type": "switch-annotation",
        "heading": "What do you want to learn more about?",
        "options": ["Quiz types", "Socratic questioning", "Learning math with Capira"],
        "reactions": [{
            "type": "showOverlay",
            "target": "quiz-types"
        }, {
            "type": "showOverlay",
            "target": "socratic-intro"
        }, {
            "type": "showOverlay",
            "target": "math-intro"
        }]
    }, {
        "id": "quiz-types",
        "type": "switch-annotation",
        "heading": "Which type of quiz do you want to learn more about?",
        "options": ["Multiple Choice Quiz", "Short Answer Quiz", "Number Range Quiz", "Hotspot Quiz", "Draw Quiz", "Custom Quiz"],
        "reactions": [{
            "type": "showOverlay",
            "target": "multi-choice-intro"
        }, {
            "type": "showOverlay",
            "target": "short-answer-intro"
        }, {
            "type": "showOverlay",
            "target": "number-range-intro"
        }, {
            "type": "showOverlay",
            "target": "hotspot-intro"
        }, {
            "type": "showOverlay",
            "target": "draw-intro"
        }, {
            "type": "showOverlay",
            "target": "custom-intro"
        }]
    }, {
        "id": "short-answer-intro",
        "type": "standard-annotation",
        "heading": "Short Answer Quiz",
        "body": "The <i>Short Answer Quiz</i> accepts a word or short phrase as an answer. <ul><li>Separate Reactions can be defined for any number of answers.</li><li>Spelling mistakes can be detected.</li></ul>.",
        "reaction": {
            "type": "showOverlay",
            "target": "short-answer"
        }
    }, {
        "id": "short-answer",
        "type": "short-answer-quiz",
        "question": "What is the capital of Germany?",
        "combinations": [{
            "value": "#nocase Berlin",
            "grade": 0,
            "feedback": "Correct! Berlin is the capital of Germany.",
            "reaction": {
                "type": "showOverlay",
                "target": 0
            }
        }, {
            "value": "#typo Berlin",
            "grade": 2,
            "feedback": "You are on the right track, but watch your spelling!"
        }, {
            "value": "#typo hamburg #and <#not #nocase homburg>",
            "grade": 2,
            "feedback": "Hamburg a big city in Germany, but it's not the capital!"
        }, {
            "value": "#nocase Paris",
            "grade": 2,
            "feedback": "Nope, that's the capital of France."
        }, {
            "value": "#typo Paris",
            "grade": 2,
            "feedback": "Do you mean \"Paris\"?"
        }, {
            "value": "#nocase Rome",
            "grade": 2,
            "feedback": "Nope, that's the capital of Italy."
        }, {
            "value": "#nocase London",
            "feedback": "Nope, that's the capital of England.",
            "grade": 2
        }, {
            "value": "#nocase Germany",
            "grade": 2,
            "feedback": "Well, no. Germany is not its own capital!"
        }, {
            "feedback": "Hint: I know 5 Cities in Europe: Paris, Rome, Hamburg, London and Berlin.",
            "grade": 2
        }]
    }, {
        "id": "multi-choice-intro",
        "type": "standard-annotation",
        "heading": "Single Answer Quiz",
        "body": "<h3>Features</h3><ul><li>Students can select one from a set of choices</li></ul><h3>Hints for the Example</h3><ul><li>Try out all of the answers</li></ul>",
        "reaction": {
            "type": "showOverlay",
            "target": "single-answer"
        }
    }, {
        "id": "single-answer",
        "question": "Why is 42 the answer to the Ultimate Question of Life, the Universe, and Everything?",
        "type": "single-answer-quiz",
        "options": ["Because 42 will do.", "Because 42 is 101010 in binary code.", "Because light refracts off water by 42 degrees to create a rainbow.", "Because light requires <latex-formula>10^{-42}</latex-formula> seconds to cross the diameter of a proton."],
        "combinations": [{
            "value": 0,
            "grade": 0,
            "feedback": "Exactly! Dougles Adams choose 42 randomly.",
            "reaction": {
                "type": "showOverlay",
                "target": "multi-answer-intro"
            }
        }, {
            "value": 1,
            "grade": 1,
            "feedback": "Do you believe he thought that deeply about every random fact? ;)",
            "reaction": {
                "type": "showOverlay",
                "target": "single-answer-hint"
            }
        }, {
            "value": 3,
            "grade": 2,
            "feedback": "Do you believe he thought that deeply about every random fact? ;)",
            "reaction": {
                "type": "showOverlay",
                "target": "single-answer-hint"
            }
        }, {
            "grade": 2,
            "feedback": "Not quite, try again!"
        }]
    }, {
        "id": "single-answer-hint",
        "type": "standard-annotation",
        "heading": "Many theories were proposed, but Douglas Adams rejected them all.",
        "body": "<i>\"The answer to this is very simple. It was a joke. It had to be a number, an ordinary, smallish number, and I chose that one. Binary representations, base thirteen, Tibetan monks are all complete nonsense. I sat at my desk, stared into the garden and thought '42 will do'. I typed it out. End of story.\"</i>",
        "reaction": {
            "type": "showOverlay",
            "target": "single-answer"
        }
    }, {
        "id": "multi-answer-intro",
        "type": "standard-annotation",
        "heading": "Multi Answer Quiz",
        "body": "<h3>Features</h3><ul><li>Students can select multiple answers from a set of choices</li><li>Feedback can be defined for each combination of choices</li></ul><h3>Hints for the Example</h3><ul><li>Try picking just one color, or color combinations that don’t make orange</li></ul>",
        "reaction": {
            "type": "showOverlay",
            "target": "multi-answer"
        }
    }, {
        "id": "multi-answer",
        "question": "Mixing which two colors makes orange?",
        "type": "multi-answer-quiz",
        "options": ["Blue", "Yellow", "Red"],
        "combinations": [{
            "value": "[1, 2]",
            "grade": 0,
            "feedback": "Correct!",
            "reaction": {
                "type": "showOverlay",
                "target": 0
            }
        }, {
            "value": "[0, 2]",
            "grade": 2,
            "feedback": "No, that's purple!"
        }, {
            "value": "[0, 1]",
            "grade": 2,
            "feedback": "No, that's green!"
        }, {
            "value": "[1] #or [2]",
            "grade": 2,
            "feedback": "Almost... what color could you add?"
        }, {
            "value": "[0]",
            "grade": 2,
            "feedback": "Hmm... how can you mix one color?"
        }]
    }, {
        "id": "number-range-intro",
        "type": "standard-annotation",
        "heading": "Number Range Quiz",
        "body": "<h3>Features</h3><ul><li>Students select a number from a pre-defined range</li><li>Separate feedback can be defined for any number or range</li></ul><h3>Hints for the Example</h3><ul><li>Try picking numbers that are close, and numbers that are completely wrong</li></ul>",
        "reaction": {
            "type": "showOverlay",
            "target": "number-range"
        }
    }, {
        "id": "number-range",
        "type": "number-range-quiz",
        "question": "How much is <latex-formula>\\pi</latex-formula>?",
        "min": 3,
        "max": 3.4,
        "step": 0.01,
        "combinations": [{
            "value": "#approx 3.14 #epsilon 0.01",
            "grade": 0,
            "feedback": "Correct! <latex-formula>\\pi\\approx 3.14</latex-formula>",
            "reaction": {
                "type": "showOverlay",
                "target": 0
            }
        }, {
            "value": "#lt 3.1 #or #gt 3.2",
            "grade": 2,
            "feedback": "That's way off!"
        }, {
            "value": "#lt 3.14",
            "grade": 2,
            "feedback": "That's a little too low..."
        }, {
            "value": "#gt 3.14",
            "grade": 2,
            "feedback": "That's a little too much..."
        }]
    }, {
        "id": "hotspot-intro",
        "type": "standard-annotation",
        "heading": "Hotspot Quiz",
        "body": "<h3>Features</h3><ul><li>Students can click directly on a video or image</li><li>Separate feedback can be defined for any location in the quiz</li></ul><h3>Hints for the Example</h3><ul><li>Try clicking on different countries, or in the ocean</li></ul>",
        "reaction": {
            "type": "showOverlay",
            "target": "hotspot"
        }
    }, {
        "id": "hotspot",
        "question": "Where is Germany on this map?",
        "type": "hotspot-quiz",
        "backgroundImage": "/static/img/user-quiz-images/europe.gif",
        "mask": "data:image/gif;base64,R0lGODlhkAHhAOf3AGxtB20zWwFyeC5kUF5eXklTUnl5eXp2cHx0a…O8PTdUQx0UworIQTuUwzdkwdiDw4/Lw68YQoSIwTtsgz9kCNnbigGKO0VjOtXItzI8wvsLCAA7",
        "combinations": [{
            "value": "#ffd479",
            "grade": 0,
            "feedback": "Yeah, that's Germany!",
            "reaction": {
                "type": "showOverlay",
                "target": 0
            }
        }, {
            "value": "#76d6ff",
            "grade": 2,
            "feedback": "Nope, that's France!"
        }, {
            "value": "#fffc79",
            "grade": 2,
            "feedback": "Nope, that's Poland!"
        }, {
            "value": "#76d6ff",
            "grade": 2,
            "feedback": "Nope, that's Switzerland!"
        }, {
            "value": "#d4fb79",
            "grade": 2,
            "feedback": "Nope, that's the Netherlands!"
        }, {
            "value": "#73fdff",
            "grade": 2,
            "feedback": "Nope, that's Sweden!"
        }, {
            "value": "#73fcd6",
            "grade": 2,
            "feedback": "Nope, that's Belgium!"
        }, {
            "value": "#005493",
            "grade": 2,
            "feedback": "Nope, that's the ocean!"
        }, {
            "value": "#0096ff",
            "grade": 2,
            "feedback": "Nope, that's Norway!"
        }, {
            "value": "#009193",
            "grade": 2,
            "feedback": "Nope, that's Finland!"
        }, {
            "value": "#fffb00",
            "grade": 2,
            "feedback": "Nope, that's Belarus!"
        }, {
            "value": "#ff2600",
            "grade": 2,
            "feedback": "Nope, that's Ukraine!"
        }, {
            "value": "#ff85ff",
            "grade": 2,
            "feedback": "Nope, that's Russia!"
        }, {
            "value": "#00f900",
            "grade": 2,
            "feedback": "Nope, that's the UK!"
        }, {
            "value": "#7a81ff",
            "grade": 2,
            "feedback": "Nope, that's Italy!"
        }, {
            "value": "#942193",
            "grade": 2,
            "feedback": "Nope, that's Iceland!"
        }, {
            "value": "#ff2f92",
            "grade": 2,
            "feedback": "Nope, that's Portugal!"
        }]
    }, {
        "id": "draw-intro",
        "type": "standard-annotation",
        "heading": "Draw Quiz",
        "body": "<h3>Features</h3><ul><li>Students can draw any shape on top of a video or image</li></ul>",
        "reaction": {
            "type": "showOverlay",
            "target": "draw"
        }
    }, {
        "id": "draw",
        "question": "Draw a circle with diameter 1 and center at (3, 0).",
        "type": "draw-quiz",
        "backgroundImage": "/static/img/user-quiz-images/coordinate-system.png",
        "combinations": [{
            "value": "data:image/gif;base64,R0lGODdhIAPCAbMAAAAAAACA/wCqqgCq1QC1zQC10gC12wC60gC7z…Y68IEQjKAEJ0jBClrwghjMoAY3yMEOevCDIAyhCEdIwhKa8IQoTKEKV8jCFrrwhTBcRwQAADs=",
            "grade": 0,
            "threshold": 0.94,
            "feedback": "Well done!",
            "reaction": {
                "type": "showOverlay",
                "target": 0
            }
        }, {
            "value": "data:image/gif;base64,R0lGODdhIAPCAbMAAAAAAACA/wCqqgCq1QC1zQC10gC12wC60gC7z…Y68IEQjKAEJ0jBClrwghjMoAY3yMEOevCDIAyhCEdIwhKa8IQoTKEKV8jCFrrwhTBcRwQAADs=",
            "threshold": 0.94,
            "grade": 2,
            "translateX": true,
            "translateY": true,
            "feedback": "The circle should be centered at (3,0)"
        }, {
            "value": "data:image/gif;base64,R0lGODdhIAPCAbMAAAAAAACA/wCqqgCq1QC1zQC10gC12wC60gC7z…Y68IEQjKAEJ0jBClrwghjMoAY3yMEOevCDIAyhCEdIwhKa8IQoTKEKV8jCFrrwhTBcRwQAADs=",
            "threshold": 0.975,
            "scale": true,
            "grade": 2,
            "feedback": "The circle should have a diameter of 1"
        }, {
            "grade": 2,
            "feedback": "Not quite, try again!"
        }]
    }, {
        "id": "custom-intro",
        "type": "standard-annotation",
        "heading": "Custom Quiz",
        "body": "<h3>Features</h3><ul><li>Quiz questions can be placed directly on an image or video</li></ul>",
        "reaction": {
            "type": "showOverlay",
            "target": "custom"
        }
    }, {
        "id": "custom",
        "question": "What is the sum of the two fractions?",
        "type": "custom-quiz",
        "backgroundImage": "/static/img/user-quiz-images/fractions.png",
        "items": [{
            "id": "numerator",
            "type": "input-item",
            "x": 0.75,
            "y": 0.1,
            "z": 1,
            "w": 0.15,
            "fontSize": 5
        }, {
            "id": "denominator",
            "type": "input-item",
            "x": 0.75,
            "y": 0.4,
            "z": 1,
            "w": 0.15,
            "fontSize": 5
        }],
        "combinations": [{
            "value": "<& @numerator/@denominator & #equals 13/15>",
            "grade": 0,
            "feedback": "Correct!",
            "reaction": {
                "type": "showOverlay",
                "target": "0"
            }
        }, {
            "value": "<& @denominator & #equals 8>",
            "grade": 2,
            "feedback": "Instead of adding the denominators, try to find a common denominator."
        }, {
            "value": "<& @denominator & #equals 15>",
            "grade": 2,
            "feedback": "The denominator is grade, but check the numerator again..."
        }, {
            "grade": 2,
            "feedback": "Not quite, try again!"
        }]
    }, {
        "id": "math-intro",
        "type": "standard-annotation",
        "heading": "Math in Capira",
        "body": "Capira has a number of features to support learning math.<h3>Features</h3><ul><li>Any text field can display LaTeX expressions.</li><li>Students can answer by typing a mathematical expression.</li></ul>Check out examples in the next quiz!",
        "reaction": {
            "type": "showOverlay",
            "target": "latex"
        }
    }, {
        "id": "latex",
        "type": "single-answer-quiz",
        "question": "Which of the following expressions is the largest?",
        "options": ["<latex-formula>\\sqrt{140}</latex-formula>", "<latex-formula>\\sum_{i=1}^{5} i</latex-formula>", "<latex-formula>4\\pi</latex-formula>", "<latex-formula>\\left(\\frac{7}{2}\\right)^2</latex-formula>", "<latex-formula>\\displaystyle f(x) = \\int_{-\\infty}^\\infty\\hat f(\\xi)\\,e^{2 \\pi i \\xi x}\\,d\\xi</latex-formula>"],
        "combinations": [{
            "value": 1,
            "grade": 0,
            "feedback": "Yes! <latex-formula>\\sum_{i=1}^{5} i = 15</latex-formula>",
            "reaction": {
                "type": "showOverlay",
                "target": "seman-intro"
            }
        }, {
            "value": 0,
            "grade": 2,
            "feedback": "Not quite.<br/><i>Hint</i>: Do you know what <latex-formula>\\sqrt{144}</latex-formula> is?"
        }, {
            "value": 2,
            "grade": 2,
            "feedback": "Not quite. <latex-formula>4\\pi\\approx 4 \\cdot 3.14 = 12.56 </latex-formula>"
        }, {
            "value": 3,
            "grade": 2,
            "feedback": "Not quite. <latex-formula>\\left(\\frac{7}{2}\\right)^2 = \\frac{7^2}{2^2}=\\frac{49}{4} \\approx 12</latex-formula>"
        }, {
            "value": 4,
            "grade": 2,
            "feedback": "<latex-formula>\\displaystyle f(x) = \\int_{-\\infty}^\\infty\\hat f(\\xi)\\,e^{2 \\pi i \\xi x}\\,d\\xi</latex-formula> <br>is just some fancy expression ;)"
        }, {
            "grade": 2,
            "feedback": "Not quite, try again!"
        }]
    }, {
        "id": "seman-intro",
        "type": "standard-annotation",
        "heading": "Math Quiz",
        "body": "Math Quizzes can be used to evaluate mathematical answers.<h3>Features</h3><ul><li>Students can enter a mathematical expression as an answer</li><li>Any answer that is mathematically equivalent to the reference answer is grade</li></ul><h3>Hints for the Example</h3><ul><li>Try playing around with different ways to write the given expression!</li></ul>",
        "reaction": {
            "type": "showOverlay",
            "target": "seman"
        }
    }, {
        "id": "seman",
        "type": "math-quiz",
        "question": "Try writing any expression that is equivalent to <latex-formula>a^2 + 2ab+b^2</latex-formula>",
        "combinations": [{
            "value": "#equals a^2+2ab+b^2",
            "grade": 0,
            "feedback": "Correct!",
            "reaction": {
                "type": "showOverlay",
                "target": "syntax-intro"
            }
        }, {
            "value": "#equals a^2-2ab+b^2",
            "grade": 2,
            "feedback": "Not quite. This is equivalent to <latex-formula>a^2 + 2ab+b^2</latex-formula>."
        }, {
            "grade": 2,
            "feedback": "Not quite, try again!"
        }]
    }, {
        "id": "syntax-intro",
        "type": "standard-annotation",
        "heading": "Math Quiz",
        "body": "Math Quizzes can also be configured to accept only answers in a specific form. This helps students understand the individual steps to solve math problems.<h3>Hints for the Example</h3><ul><li>Try copying the expression given in the question exactly</li><li>Try changing the order of the expressions in the grade answer</li></ul>",
        "reaction": {
            "type": "showOverlay",
            "target": "syntax"
        }
    }, {
        "id": "syntax",
        "type": "math-quiz",
        "question": "Multiply out the expression <latex-formula>a\\left(b+c\\right)</latex-formula>",
        "combinations": [{
            "value": "#identic ab+ac",
            "grade": 0,
            "feedback": "Correct!",
            "reaction": {
                "type": "showOverlay",
                "target": 0
            }
        }, {
            "value": "#equals a(b + c)",
            "grade": 2,
            "feedback": "You need to expand the expression!"
        }, {
            "grade": 2,
            "feedback": "Not quite, try again!"
        }]
    }, {
        "id": "socratic-intro",
        "type": "standard-annotation",
        "heading": "Socratic Questioning with Capira",
        "body": "coming soon...",
        "reaction": {
            "type": "showOverlay",
            "target": 0
        }
    }]
};
}

utils.unitFactory.getBrokenUnit = function() {
    return {};
}

utils.fetchUnitAs = function(unit, user, done, onResponse) {
    utils.request('GET', '/unit/' + unit.id, null,
        function(error, response, body) {
            utils.fullExpectationsOnUnit(error, response, body, unit);
            if (onResponse) onResponse(error, response, body);
            if (done) done();
            utils.log('Fetch Unit', user);
        }, user);
}

utils.createUnitAs = function(unit, user, done, onResponse) {
    utils.request('POST', '/unit', unit,
        function(error, response, body) {
            onResponse(error, response, body);
            if (done) done();
            utils.log('Create Unit', user);
        }, user);
}


utils.updateUnitAs = function(unit, user, done, onResponse) {
    utils.request('PUT', '/unit', unit,
        function(error, response, body) {
            onResponse(error, response, body);
            if (done) done();
            utils.log('Update Unit', user);
        }, user);
}

utils.updateUnitParentAs = function(unit, user, done, onResponse) {
    utils.request('PUT', '/unit/parent', unit,
        function(error, response, body) {
            onResponse(error, response, body);
            if (done) done();
            utils.log('Update Unit Parent', user);
        }, user);
}

utils.deleteChannelAs = function(unit, user, done, onResponse) {
    utils.request('DELETE', '/unit/' + unit.id, null,
        function(error, response, body) {
            onResponse(error, response, body);
            if (done) done();
            utils.log('Delete Unit', user);
        }, user);
}


module.exports=utils;

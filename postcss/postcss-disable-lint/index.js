const {comment, plugin, root} = require('postcss')

const workers = {
    before: function commentBefore(decl, commentData) {
        // задача: склеить и добавить переводов строк так, чтобы
        // при любых исходных ситуациях наше правило оказалось на отдельной
        // строке с комментарием на той же строке перед ним

        let next = decl.next()

        // добавляем перевод строки после текущего правила
        if (next) {
            // добавляем перевод строки следующему после decl правилу, если оно существует.
            // (почему-то decl.raws.after = "\n" не работает)
            next.raws.before = "\n"
        } else {
            // если следуюшего правила нет - добавляем в родителя
            decl.parent.raws.after = "\n"
        }

        // если у правила есть перевод строки до него - убираем.
        // между ним и комментарием должен быть только пробел
        decl.raws.before = ' '
        // между названием правила и его значением не должно быть переводов строк,
        // а только двоеточие и пробел
        decl.raws.between = ": "

        // добавляем комментарий перед правилом на той же строке, с переводом строки
        // перед ним
        decl.before(comment({text: commentData, raws: {before: "\n"}}));

        // в итоге получается так:
        //
        // <перевод строки> /* комментарий */ правило: значение; <перевод строки>
    },
    wrap: function commentBefore(decl, commentData) {
        // задача: склеить и добавить переводов строк так, чтобы
        // при любых исходных ситуациях наше правило оказалось на отдельной
        // строке с комментарием на той же строке перед ним и после него

        let next = decl.next()

        // добавляем перевод строки после текущего правила
        if (next) {
            // добавляем перевод строки следующему после decl правилу, если оно существует.
            // (почему-то decl.raws.after = "\n" не работает)
            next.raws.before = "\n"
        } else {
            // если следуюшего правила нет - добавляем в родителя
            decl.parent.raws.after = "\n"
        }

        // если у правила есть перевод строки до него - убираем.
        // между ним и комментарием должен быть только пробел
        decl.raws.before = ' '
        // между названием правила и его значением не должно быть переводов строк,
        // а только двоеточие и пробел
        decl.raws.between = ": "

        // добавляем комментарий перед правилом на той же строке, с переводом строки
        // перед ним
        decl.before(comment({text: commentData.before, raws: {before: "\n"}}));
        decl.after(comment({text: commentData.after, raws: {before: " "}}));

        // в итоге получается так:
        //
        // <перевод строки> /* комментарий */ правило: значение; /* комментарий */ <перевод строки>
    }

}

module.exports = plugin('postcss-disable-lint', function (patterns) {
    return function (ast, result) {
        ast.walkDecls(function (decl) {
            const pattern = patterns.find(function (pattern) {
                if (pattern.type && pattern.type !== decl.type) return
                if (pattern.prop !== decl.prop) return

                const value_matched = (pattern.value instanceof RegExp)
                    ? decl.value.match(pattern.value)
                    : decl.value === pattern.value

                return value_matched
            })

            if (!pattern) return

            const method = pattern.method ? pattern.method : 'before'

            workers[method](decl, pattern.comment)
        })

        ast.walkAtRules(function (atrule) {
            const pattern = patterns.find(function (pattern) {
                if (pattern.type && pattern.type !== atrule.type) return
                if (pattern.name !== atrule.name) return

                return true
            })

            if (!pattern) return

            let conflicter

            if ((conflicter = isNodeStartConflicts(atrule, atrule.prev())) || (conflicter = isNodeStartConflicts(atrule, atrule.next()))) {
                // пока нельзя подключить sourcemaps https://github.com/olegskl/gulp-stylelint/pull/47
                // мы вынуждены вставлять комментарии на ту же строку, что и правило
                // и если мы не можем это сделать - то всё
                console.log()
                console.log(result.opts.from, `line ${atrule.source.start.line}`)
                console.log(`Atrule ${atrule.name}`
                    + ` is placed at the same line as ${conflicter.type} ${conflicter.name}`
                )
                console.log()
                throw "Atrules and other rules should not be placed on same lines, failed to disable lint"
            }

            atrule.raws.before = atrule.raws.before + ` /* stylelint-disable-line ${pattern.disable} */ `
        })

        function isNodeStartConflicts(node, conflicter) {
            if (!node || !conflicter) return false

            const same_line =
                (node.source.start.line === conflicter.source.start.line)
                || (node.source.start.line === conflicter.source.end.line)

            return same_line ? conflicter : false
        }


    };
});

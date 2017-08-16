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

module.exports = plugin('postcss-add-comments', function (patterns) {
    return function (ast) {
        ast.walkDecls(function (decl) {
            const pattern = patterns.find(function (pattern) {
                if (pattern.prop !== decl.prop) return

                const value_matched = (pattern.value instanceof RegExp)
                    ? decl.value.match(pattern.value)
                    : decl.value === pattern.value

                return value_matched
            })

            if (!pattern) return

            const method = pattern.method ? pattern.method : 'before'

            workers[method](decl, pattern.comment)
        });
    };
});

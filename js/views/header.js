window.HeaderView = Backbone.View.extend({

    initialize: function () {
        this.render();
    },

    render: function () {
        $(this.el).html(this.template());
        return this;
    },

    selectMenuItem: function (menuItem) {
        $('.nav li').removeClass('active');
        if (menuItem) {
            $('.' + menuItem).addClass('active');
        }
    },

    showEditTab: function (id) {
        if (id) {
            $('.nav .edit-menu').removeClass('invisible');
            $('.nav .edit-menu > a').attr('href', '#photos/' + id + '/edit');
        } else {
            $('.nav .edit-menu').addClass('invisible');
        }
    }

});
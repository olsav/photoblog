/**
TODO: 
add requirejs
add router and organize views and models
image resize
**/
var AppRouter = Backbone.Router.extend({

    routes: {
        ""                  : "list",
        "photos/page/:page"	: "list",
        "photos/add"        : "addPhoto",
        "photos/:id/edit"   : "editPhoto",
        "photos/:id"        : "viewPhoto"
    },

    initialize: function () {
        this.headerView = new HeaderView();
        $('.header').html(this.headerView.el);
    },

	list: function(page) {
        var p = page ? parseInt(page, 10) : 1;
        var photoList = new PhotoCollection();
        photoList.fetch({success: function(){
            $("#content").html(new PhotoListView({model: photoList, page: p}).el);
        }});
        this.headerView.showEditTab();
        this.headerView.selectMenuItem('home-menu');
    },

    viewPhoto: function(id) {
        var photo = new Photo({id: id});
        photo.fetch({success: function(){
            $("#content").html(new PhotoView({model: photo}).el);
        }});
        this.headerView.showEditTab(id);
        this.headerView.selectMenuItem();
    },

    editPhoto: function (id) {
        var photo = new Photo({id: id});
        photo.fetch({success: function(){
            $("#content").html(new PhotoEdit({model: photo}).el);
        }});
        this.headerView.showEditTab(id);
        this.headerView.selectMenuItem('edit-menu');
    },

	addPhoto: function() {
        var photo = new Photo();
        $('#content').html(new PhotoEdit({model: photo}).el);
        this.headerView.showEditTab();
        this.headerView.selectMenuItem('add-menu');
	}

});

utils.loadTemplate(['HeaderView', 'PhotoEdit', 'PhotoView', 'PhotoListItemView'], function() {
    app = new AppRouter();
    Backbone.history.start();
});
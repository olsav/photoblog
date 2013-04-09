window.PhotoEdit = Backbone.View.extend({

    initialize: function () {
        this.render();
    },

    render: function () {
        $(this.el).html(this.template(this.model.toJSON()));
        this.createUploader();

        return this;
    },

    pictureFile: null,

    events: {
        "change"        : "change",
        "click .save"   : "beforeSave",
        "click .delete" : "deleteNode"
        //"change input[name=file]" : "fileUploadHandler"
    },

    change: function (event) {
        // Remove any existing alert message
        utils.hideAlert();

        // Apply the change to the model
        var target = event.target;
        var change = {};
        change[target.name] = target.value;
        this.model.set(change);

        // Run validation rule (if any) on changed item
        var check = this.model.validateItem(target.id);
        if (check.isValid === false) {
            utils.addValidationError(target.id, check.message);
        } else {
            utils.removeValidationError(target.id);
        }
    },

    beforeSave: function () {
        var self = this;
        var check = this.model.validateAll();
        if (check.isValid === false) {
            utils.displayValidationErrors(check.messages);
            return false;
        }
        // Upload picture file if a new file was dropped in the drop area
        if (this.pictureFile) {
            //Assign filepath and thumbnail to model
            this.model.set("picture", this.pictureFile.filepath);
            this.model.set("thumb", this.pictureFile.thumbnail);
            this.saveNode();
        } else {
            this.saveNode();
        }
        return false;
    },

    saveNode: function () {
        var self = this;
        this.model.set("created", utils.getDate());
        this.model.set("teaser", this.model.get('body').substring(160, 0));
        this.model.save(null, {
            success: function (model) {
                self.render();
                app.navigate('photos/' + model.id, true);
                utils.showAlert('Success!', 'Photo saved successfully', 'alert-success');
            },
            error: function () {
                utils.showAlert('Error', 'An error occurred while trying to delete this item', 'alert-error');
            }
        });
    },

    deleteNode: function () {
        this.model.destroy({
            success: function () {
                alert('Node deleted successfully');
                //window.history.back();
                app.navigate('', true);
            }
        });
        return false;
    },

    createUploader: function () {
        var self = this;
        var uploader = new qq.FineUploader({
          element: this.$('#fine-uploader')[0],
          request: {
            endpoint: 'api/upload.php'
          },
          multiple: false,
          //itemLimit: 5,
          sizeLimit: 1048576,
          validation: {
            allowedExtensions: ['jpeg', 'jpg', 'png']
          },
          callbacks: {
            onComplete: function(id, fileName, responseJSON) {
              if (responseJSON.success) {
                self.pictureFile = responseJSON;
                $('#thumbnail-fine-uploader').append('<img src="' + responseJSON.thumbnail + '" id="thumb" alt="' + fileName + '">');
              }
            }
          }
        });
    }

});
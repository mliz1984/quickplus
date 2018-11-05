$(function(){
jQuery.validator.addMethod("quickAjax",  function( value, element, param ) {
			if ( this.optional( element ) ) {
				return "dependency-mismatch";
			}
			var previous = this.previousValue( element ),
				validator, data;

			if (!this.settings.messages[ element.name ] ) {
				this.settings.messages[ element.name ] = {};
			}
			previous.originalMessage = this.settings.messages[ element.name ].remote;
			this.settings.messages[ element.name ].remote = previous.message;

			param = typeof param === "string" && { url: param } || param;

			if ( previous.old === value ) {
				return previous.valid;
			}

			previous.old = value;
			validator = this;
			this.startRequest( element );
			data = {};
			data[ element.name ] = value;
			$.ajax( $.extend( true, {
				mode: "abort",
				port: "validate" + element.name,
				dataType: "json",
				data: data,
				context: validator.currentForm,
				success: function( response ) {
					 var tempResponse = response;
                        if (tempResponse.success != undefined) {
                            response = tempResponse.success;
                        }
                       
					var valid = response === true || response === "true",
						errors, message, submitted;
						if (tempResponse.message != undefined) {
							
                            validator.settings.messages[element.name].quickAjax = tempResponse.message;
                        } else {
                        	
                            validator.settings.messages[element.name].quickAjax = previous.originalMessage;
                        }
				//	validator.settings.messages[ element.name ].remote = previous.originalMessage;
					if ( valid ) {
						submitted = validator.formSubmitted;
						validator.prepareElement( element );
						validator.formSubmitted = submitted;
						validator.successList.push( element );
						delete validator.invalid[ element.name ];
						validator.showErrors();
					} else {
						errors = {};
					 	
						message = tempResponse.message || response || validator.defaultMessage( element, "quickAjax" );
 						
						errors[ element.name ] = previous.message = $.isFunction( message ) ? message( value ) : message;
						validator.invalid[ element.name ] = true;
						validator.showErrors( errors );
					}
					previous.valid = valid;
					validator.stopRequest( element, valid );
				}
			}, param ) );
			return "pending";
		}, "Please set a warning message"); 
});
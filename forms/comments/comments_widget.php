<div id="commentsWidget">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item active"><a href="#commentsList" class="nav-link active list" data-toggle="tab">{{_LANG[comments]}}</a></li>
            <li class="nav-item"><a href="#commentsEdit" class="nav-link form" data-toggle="tab">{{_LANG[add]}}</a></li>
        </ul>
    <div class="tab-content pt-3">
        <div class="tab-pane fade in active" id="commentsList" role="tabpanel">
            <div data-wb-role="foreach" data-wb-table="comments" data-wb-size="15" data-wb-sort="date:d" data-wb-where='active = "on" AND (
				(target_form = "{{_ENV[route][form]}}" AND target_item = "{{_ENV[route][item]}}") )' data-wb-hide="wb">
                <div class="row">
                    <div class="col-sm-2" data-wb-role="formdata" data-wb-form="users" data-wb-item="{{user_id}}">
                        <img class="comment-avatar img-responsive" data-wb-role="thumbnail" size="100px;100px;src"
				style="max-width:100px;"
				src="/uploads/users/{{id}}/{{avatar[0][img]}}" data-wb-noimg="/engine/uploads/__system/person.svg">
                        <meta role="variable" var="name" value="{{name}}">
                    </div>
                    <div data-wb-role="where" data='user_id=""' data-wb-hide="*">
                        <meta role="variable" var="name" value="{{name}}">
                    </div>
                    <div class="col-sm-10">
                        <div class="comment-header">
                            <span class="comment-rating"><input type="hidden" readonly class="rating" value="{{rating}}"></span>
                            <i class="fa fa-user"></i> <span class="user">{{_VAR[name]}} </span>
                            &nbsp;<i class="fa fa-calendar"></i> <span class="date">{{date}} </span>
                        </div>
                        <div class="comment-body">
                            <p><i class="fa fa-comment"></i> {{text->nl2br()}}</p>
                        </div>
                        <div class="comment-reply pl-5" data-wb-role="where" data=' reply > "" '>
                            <span class="user"><i class="fa fa-user"></i> {{_LANG[admin]}}</span>
                            <p><i class="fa fa-comment"></i> {{reply->nl2br()}}</p>
                        </div>
                    </div>
                </div>
                <empty>
			<div class="row">
				<div class="col-xs-12">
				<div class="alert alert-warning show">
					{{_LANG[empty]}}
				</div>
				</div>
			</div>
		</empty>
                <div class="clearfix">&nbsp;</div>
            </div>
        </div>
        <div class="tab-pane fade" id="commentsEdit" data-wb-role="formdata" data-wb-table="users" data-wb-item="{{_SESS[user_id]}}" role="tabpanel">
            <div data-wb-role="include" id="commentsEditInc" src="form" data-wb-name="comments_form"></div>
            <meta data-wb-selector="form#commentsEditForm" data-wb-attr="data-wb-item" value="_new">
            <div class="alert alert-success hidden">{{_LANG[success]}}</div>
            <div class="alert alert-danger hidden">{{_LANG[error]}}</div>
        </div>
    </div>
</div>
<script language="javascript" src="/engine/forms/comments/comments.js" data-wb-append="body"></script>
<script type="text/locale" data-wb-role="include" src="comments_common"></script>
<style>
    #commentsList .comment-header .comment-rating{
        float:right;
    }
    #commentsList .comment-avatar {
        width:100%;
        height: auto;
        width: 100%;
        height: auto;
        border-radius: 500px;
    }
    #commentsWidget li:before {content:''!important;padding-right:0!important;}
    #commentsList .nav {position: relative!important;}
</style>

<div id="commentsWidget">
    <div class="tab-content">
        <div class="tab-pane active" id="commentsList">
        <ul class="nav nav-tabs">
            <li class="active hidden"><a href="#commentsList" data-toggle="tab">Отзывы</a></li>
            <li class="pull-right"><a href="#commentsEdit" data-toggle="tab">Написать отзыв</a></li>
        </ul>

            <div data-wb-role="foreach" data-wb-table="comments" data-wb-size="15" data-wb-sort="date:d" data-wb-where='active = "on" AND (
				(target_form = "{{_ENV[route][form]}}" AND target_item = "{{_ENV[route][item]}}") OR
				(target_form = "comments"))'>
                <div class="row">
                    <div class="col-sm-2" data-wb-role="formdata" data-wb-form="users" data-wb-item="{{user_id}}">
                        <img class="comment-avatar img-responsive" data-wb-role="thumbnail" size="100px;100px;src" src="/uploads/users/{{id}}/{{avatar[0][img]}}" noimg="/engine/uploads/__system/person.svg">
                        <meta role="variable" var="name" value="{{name}}">
                    </div>
                    <div role="where" data='user_id=""' data-wb-hide="*">
                        <meta role="variable" var="name" value="{{name}}">
                    </div>
                    <div class="col-sm-10">
                        <div class="comment-header">
                            <span class="comment-rating"><input type="hidden" readonly class="rating" value="{{rating}}"></span>
                            <span class="user">{{_VAR[name]}} </span>
                            <span class="date">{{date}} </span>
                        </div>
                        <div class="comment-body">
                            <p>{{text}}</p>
                        </div>
                        <div class="comment-reply" data-wb-role="where" data=' reply > "" '>
                            <span class="user"><i class="fa fa-comment"></i> Админ</span>
                            <p>{{reply}}</p>
                        </div>
                    </div>
                </div>
                <div class="clearfix">&nbsp;</div>
            </div>
        </div>
        <div class="tab-pane" id="commentsEdit" data-wb-role="formdata" data-wb-table="users" data-wb-item="{{_SESS[user_id]}}">
            <div data-wb-role="include" id="commentsEditInc" src="/engine/forms/comments/comments_form.php"></div>
            <div class="alert alert-success hidden">Ваш отзыв успешно отправлен Администратору!</div>
            <div class="alert alert-danger hidden">Ваш отзыв не получилось отправить. Попробуйте позже!</div>
        </div>
    </div>
</div>
<script language="javascript" src="/engine/forms/comments/comments.js" data-wb-append="body"></script>
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
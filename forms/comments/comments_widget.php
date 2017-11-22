<div id="commentsWidget">
	<ul class="nav nav-tabs">
	  <li class="active"><a href="#commentsList" data-toggle="tab">Отзывы</a></li>
	  <li><a href="#commentsEdit" data-toggle="tab">Написать отзыв</a></li>
	</ul>
	<div class="tab-content">
		<br>
	  <div class="tab-pane active" id="commentsList">
		<div data-role="foreach" form="comments" data-size="5" data-sort="date:d" where='visible = "on" AND (
				(target_form = "{{_GET[form]}}" AND target_id = "{{_GET[id]}}") OR
				(target_form = "comments"))'>
			<div class="row">
					<div class="col-sm-2" data-role="formdata" form="users" item="{{user_id}}">
						<div class="text-center"><small><i class="glyphicon glyphicon-calendar"></i> {{date_short}}</small></div>
						<img data-role="thumbnail" size="100px;100px" src="0" noimg="/engine/uploads/__system/person.svg" class="img-responsive" alt="">
						<div class="text-center"><input type="hidden" readonly class="rating" value="{{rating}}"></div>
					</div>
					<div class="col-sm-10">
						<p>
						<i class="glyphicon glyphicon-user"> </i> <strong>{{name}}</strong></p>
						<p>{{text}}</p>
						<div data-role="where" data=" reply > '' ">
							<p>
							<i class="glyphicon glyphicon-comment"></i> <strong>Админ</strong>
							</p>
							<p>{{reply}}</p>
						</div>
					</div>
				</div>
				<div class="clearfix">&nbsp;</div>
		</div>
	  </div>
		<div class="tab-pane" id="commentsEdit" data-role="formdata" form="users" item="{{_SESS[user_id]}}">
			<div data-role="include" id="commentsEditInc" src="/engine/forms/comments/comments_edit.php"></div>
			<div class="alert alert-success hidden">Ваш отзыв успешно отправлен Администратору!</div>
			<div class="alert alert-danger hidden">Ваш отзыв не получилось отправить. Попробуйте позже!</div>
		</div>
	</div>
</div>
<script language="javascript" src="/engine/forms/comments/comments.js" append="body"></script>

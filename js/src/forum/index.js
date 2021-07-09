import { extend, override } from 'flarum/common/extend';
import app from 'flarum/common/app';

import HeaderSecondary from "flarum/components/HeaderSecondary";
import SettingsPage from "flarum/components/SettingsPage";
import Button from 'flarum/components/Button';

import LogInModal from "flarum/components/LogInModal";
import AuthLogInModal from "./components/AuthLogInModal";

const translationPrefix = 'andreybrigunet-auth-easy4live.forum.';

app.initializers.add('andreybrigunet-auth-easy4live', () => {

	extend(HeaderSecondary.prototype, 'items', addLoginLink);
	extend(HeaderSecondary.prototype, 'items', removeIfOnlyUse);
	extend(LogInModal.prototype, 'content', overrideModal);

	extend(SettingsPage.prototype, 'accountItems', removeProfileActions);
	extend(SettingsPage.prototype, 'settingsItems', checkRemoveAccountSection);

	function overrideModal() {
		if (app.forum.attribute('andreybrigunet-auth-easy4live.onlyUse')) {
			LogInModal.prototype.content = AuthLogInModal.prototype.content
			LogInModal.prototype.title = AuthLogInModal.prototype.title
			LogInModal.prototype.body = AuthLogInModal.prototype.body
			LogInModal.prototype.fields = AuthLogInModal.prototype.fields
			LogInModal.prototype.footer = AuthLogInModal.prototype.footer
			LogInModal.prototype.onsubmit = AuthLogInModal.prototype.onsubmit
		}
	}

	function addLoginLink(items) {
		if (items.has('logIn')) {
			items.add('logInLDAP',
				Button.component(
					{
						className: 'Button Button--link',
						onclick: () => app.modal.show(AuthLogInModal)
					},
					app.translator.trans(translationPrefix + 'log_in_with', 
						{ server: app.forum.attribute('andreybrigunet-auth-easy4live.method_name')})
				),
				0
			);
		}
	}

	function removeIfOnlyUse(items) {
		if (app.forum.attribute('andreybrigunet-auth-easy4live.onlyUse')) {
			if (items.has('signUp')) {
				items.remove('signUp');
			}
			if (items.has('logIn')) {
				items.remove('logIn');
			}
		}
	}

	function removeProfileActions(items) {
		items.remove('changeEmail');
		items.remove('changePassword');
	}

	function checkRemoveAccountSection(items) {
		if (items.has('account') &&
			items.get('account').children.length === 0) {
			items.remove('account');
		}
	}
});

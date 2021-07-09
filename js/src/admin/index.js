import app from 'flarum/app';

const settingsPrefix = 'andreybrigunet-auth-easy4live.';
const translationPrefix = 'andreybrigunet-auth-easy4live.admin.settings.';

app.initializers.add('andreybrigunet-auth-easy4live', function(app) {
  app.extensionData
    .for('andreybrigunet-auth-easy4live')
    .registerSetting(
      {
        setting: settingsPrefix + 'domain',
        label: app.translator.trans(translationPrefix + 'domain'),
        type: 'text',
        placeholder: 'https://site.com',
      }
    )
    .registerSetting(
      {
        setting: settingsPrefix + 'request',
        label: app.translator.trans(translationPrefix + 'request'),
        type: 'text',
        placeholder: '/',
      }
    )
    .registerSetting(
      {
        setting: settingsPrefix + 'onlyUse',
        label: app.translator.trans(translationPrefix + 'onlyUse'),
        type: 'boolean',
        default: false,
      }
    )
});

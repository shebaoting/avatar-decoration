import Extend from 'flarum/common/extenders';
import Model from 'flarum/common/Model';
import User from 'flarum/common/models/User';
import AvatarEditorPage from './components/AvatarEditorPage';

export default [
  new Extend.Routes().add('avatar-decoration.edit', '/avatar/edit', AvatarEditorPage),
  new Extend.Model(User).attribute('avatarDecoration').attribute('canEditAvatarDecoration').attribute('avatarDecorationUpdatedAt', Model.transformDate),
];

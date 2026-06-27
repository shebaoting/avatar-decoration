import Component from 'flarum/common/Component';
import classList from 'flarum/common/utils/classList';
import { assetUrl, composeAvatarLayers, DEFAULT_COLORS, emptyDecoration } from '../utils/avatarState';

export default class AvatarCanvas extends Component {
  view() {
    const manifest = this.attrs.manifest || {};
    const decoration = this.attrs.decoration || emptyDecoration();
    const colors = decoration.colors || {};
    const { layers } = composeAvatarLayers(decoration, manifest);
    const defaultAvatar = manifest.defaultAvatar || this.attrs.defaultAvatar;
    const compact = !!this.attrs.compact;
    const portrait = !!this.attrs.portrait;
    const showBackground = this.attrs.showBackground ?? !compact;
    const background = colors.background || DEFAULT_COLORS.background;

    return (
      <div className={classList('AvatarDecorationCanvas', compact && 'AvatarDecorationCanvas--compact', portrait && 'AvatarDecorationCanvas--portrait', this.attrs.className)}>
        <div className="AvatarDecorationCanvas-stage" style={showBackground ? { background } : null}>
          {!layers.length && defaultAvatar?.url && <img className="AvatarDecorationCanvas-layer AvatarDecorationCanvas-base" src={assetUrl(defaultAvatar, colors)} alt="" />}
          {layers.map((asset) => (
            <img
              className="AvatarDecorationCanvas-layer"
              src={assetUrl(asset, colors)}
              alt=""
              loading={compact ? 'lazy' : 'eager'}
              key={`${asset.slot}-${asset.path}-${asset.layer}`}
            />
          ))}
        </div>
      </div>
    );
  }
}

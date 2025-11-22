#!/bin/bash

# Assumes the USB is being mounted at /home/pi/mimmo/usb in the fstab:
# /dev/sda1 /home/pi/mimmo/usb ext4 defaults,nofail 0 2

# Clean up any existing mount
fusermount -u /home/pi/mimmo/sshfs 2>/dev/null || true

# Mount remote folder using sshfs
sshfs USER@SERVER:/path/to/remote/folder /home/pi/mimmo/sshfs

# Copy the new files from the sshfs folder to the usb
rsync --ignore-existing --progress /home/pi/mimmo/sshfs/ /home/pi/mimmo/usb/media/

# unmount the sshfs folder
fusermount -u /home/pi/mimmo/sshfs

# Start vlc
# When debugging, take out --no-qt-error-dialogs!
vlc \
--loop \
--ignore-filetypes=m3u,db,nfo,ini,ljpg,pgm,pgmyuv,pbm,pam,tga,bmp,pnm,xpm,xcf,pcx,tif,tiff,lbm,sfv,txt,sub,idx,srt,cue,ssa \
--image-duration=10.00 \
--no-video-title-show \
--no-qt-error-dialogs \
--fullscreen \
--playlist-tree "/home/pi/mimmo/usb/media/"
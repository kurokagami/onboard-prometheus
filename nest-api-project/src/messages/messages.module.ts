import { Module } from '@nestjs/common';
import { MessagesService } from './messages.service';
import { MessagesController } from './messages.controller';
import { EmailService } from 'services/email/email.service';

@Module({
  controllers: [MessagesController],
  providers: [MessagesService, EmailService],
})
export class MessagesModule {}

import { Controller, Post, Body } from '@nestjs/common';
import { MessagesService } from './messages.service';
import { CreateMessageDto } from './messages.dto';

@Controller('api-messages')
export class MessagesController {
  constructor(private readonly messagesService: MessagesService) {}
  

  @Post('/send')
  async receiveMessage(@Body() messageData: CreateMessageDto) {
    try {
      // Processar a mensagem e enviar o email
      await this.messagesService.processAndSendEmail(messageData);
      return { message: 'Email enviado com sucesso!' };
    } catch (error) {
      // Tratar erro
      console.error('Erro ao enviar email:', error);
      return { message: 'Erro ao enviar email.', error: error.message };
    }
  }
}

